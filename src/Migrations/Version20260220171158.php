<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260220171158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert fos_user.keycloakGroup legacy array values to JSON and change column type to JSON';
    }

    public function up(Schema $schema): void
    {
        // 1) Erst Daten so umwandeln, dass sie JSON-valid sind
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, keycloakGroup FROM fos_user WHERE keycloakGroup IS NOT NULL'
        );

        foreach ($rows as $row) {
            $id = $row['id'];
            $val = $row['keycloakGroup'];

            // Schon gültiges JSON? Dann nichts tun.
            $isValid = (int) $this->connection->fetchOne('SELECT JSON_VALID(?)', [$val]);
            if ($isValid === 1) {
                continue;
            }

            $decoded = null;

            // Versuch 1: PHP serialized (typisch bei DC2Type:array)
            $tmp = @unserialize((string) $val);
            if ($tmp !== false || $val === 'b:0;') {
                if ($tmp === null) {
                    $decoded = null;
                } elseif (is_array($tmp)) {
                    $decoded = $tmp;
                } else {
                    $decoded = [$tmp];
                }
            } else {
                // Versuch 2: Komma-separiert (Fallback)
                $parts = array_values(array_filter(
                    array_map('trim', explode(',', (string) $val)),
                    static fn ($x) => $x !== ''
                ));
                $decoded = $parts ?: null;
            }

            $json = $decoded === null ? null : json_encode($decoded, JSON_THROW_ON_ERROR);

            $this->addSql(
                'UPDATE fos_user SET keycloakGroup = ? WHERE id = ?',
                [$json, $id]
            );
        }

        // 2) Jetzt erst Column auf JSON umstellen
        $this->addSql('ALTER TABLE fos_user CHANGE keycloakGroup keycloakGroup JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Zurück auf LONGTEXT (wie vorher)
        $this->addSql("ALTER TABLE fos_user CHANGE keycloakGroup keycloakGroup LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)'");
    }
}
