<?php

namespace App\Service;

use App\Entity\Standort;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class MailerService
{
    private ?MailerInterface $customMailer = null;
    private ?string $userName = null;

    public function __construct(private LicenseService $licenseService, private LoggerInterface $logger, private ParameterBagInterface $parameter, private MailerInterface $mailer, private KernelInterface $kernel)
    {
    }

    public function buildTransport(Standort $server): void
    {
        if (!$server->getSmtpHost()) {
            return;
        }

        if ($this->userName === $server->getSmtpUsername() && $this->customMailer !== null) {
            return;
        }

        $this->logger->info('Build new Transport: '.$server->getSmtpHost());
        $dsn = sprintf(
            '%s://%s:%s@%s:%d',
            $server->getSmtpEncryption() ?: 'smtp',
            rawurlencode((string) $server->getSmtpUsername()),
            rawurlencode((string) $server->getSmtpPassword()),
            $server->getSmtpHost(),
            $server->getSmtpPort()
        );
        $transport = Transport::fromDsn($dsn);
        $this->customMailer = new Mailer($transport);
        $this->userName = $server->getSmtpUsername();
    }

    public function sendEmail($to, $betreff, $content, Standort $server, $attachment = []): bool
    {
        $res = true;

        try {
            $this->logger->info('Mail To: '.$to);
            $res = $this->sendViaMailer($to, $betreff, $content, $server, $attachment);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $res = false;
        }

        return $res;
    }

    private function sendViaMailer($to, $betreff, $content, Standort $server, $attachment = []): bool
    {
        if (!$to) {
            return true;
        }

        $this->buildTransport($server);

        if ($server->getSmtpHost() && $this->licenseService->verify($server)) {
            $sender = $server->getSmtpEmail();
            $senderName = $server->getSmtpSenderName();
        } else {
            $sender = $this->parameter->get('registerEmailAdress');
            $senderName = $this->parameter->get('registerEmailName');
        }

        $email = (new Email())
            ->subject((string) $betreff)
            ->from(new Address((string) $sender, (string) $senderName))
            ->to(...(array) $to)
            ->html((string) $content);

        foreach ($attachment as $data) {
            $email->addPart(new DataPart($data['body'], $data['filename'], $data['type']));
        }

        try {
            if ($server->getSmtpHost() && $this->customMailer !== null) {
                if ($this->kernel->getEnvironment() === 'dev') {
                    $email->to(...(array) $this->parameter->get('delivery_addresses'));
                }
                $this->customMailer->send($email);
            } else {
                $this->mailer->send($email);
            }
        } catch (\Throwable $e) {
            $this->mailer->send($email);
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }
}
