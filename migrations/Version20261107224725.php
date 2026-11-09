<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261107224725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY `fk_offers_category`');
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY `fk_offers_commerce`');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY `fk_reservations_client`');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY `fk_reservations_commerce`');
        $this->addSql('ALTER TABLE reservation_items DROP FOREIGN KEY `fk_items_offer`');
        $this->addSql('ALTER TABLE reservation_items DROP FOREIGN KEY `fk_items_reservation`');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY `fk_reviews_client`');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY `fk_reviews_commerce`');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY `fk_reviews_reservation`');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE offers');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE reservation_items');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('ALTER TABLE commerces DROP FOREIGN KEY `fk_commerces_user`');
        $this->addSql('ALTER TABLE commerces DROP description, DROP postal_code, DROP latitude, DROP longitude, DROP logo, DROP cover_image, DROP opening_hours, DROP rejection_reason, CHANGE business_type business_type VARCHAR(20) NOT NULL, CHANGE verification_status verification_status VARCHAR(20) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE commerces ADD CONSTRAINT FK_DFD4704EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commerces RENAME INDEX user_id TO UNIQ_DFD4704EA76ED395');
        $this->addSql('DROP INDEX email ON users');
        $this->addSql('ALTER TABLE users DROP profile_image, CHANGE role role VARCHAR(20) NOT NULL, CHANGE is_active is_active TINYINT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE verification_codes DROP FOREIGN KEY `fk_vc_user`');
        $this->addSql('ALTER TABLE verification_codes CHANGE channel channel VARCHAR(10) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE verification_codes ADD CONSTRAINT FK_7B56601DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE verification_codes RENAME INDEX idx_vc_user TO IDX_7B56601DA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, is_active TINYINT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX name (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE offers (id INT AUTO_INCREMENT NOT NULL, commerce_id INT NOT NULL, category_id INT NOT NULL, name VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, quantity_initial INT NOT NULL, quantity_available INT NOT NULL, original_price NUMERIC(10, 2) NOT NULL, discounted_price NUMERIC(10, 2) NOT NULL, unit_weight_grams INT DEFAULT NULL, available_date DATE NOT NULL, start_time TIME NOT NULL, pickup_deadline TIME NOT NULL, expiry_date DATE DEFAULT NULL, status ENUM(\'DRAFT\', \'ACTIVE\', \'SOLD_OUT\', \'EXPIRED\', \'CANCELLED\') CHARACTER SET utf8mb4 DEFAULT \'ACTIVE\' NOT NULL COLLATE `utf8mb4_general_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_offers_category (category_id), INDEX idx_offers_commerce (commerce_id), INDEX idx_offers_date (available_date), INDEX idx_offers_status (status), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservations (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, commerce_id INT NOT NULL, reservation_number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, total_amount NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, status ENUM(\'PENDING\', \'CONFIRMED\', \'PICKED_UP\', \'CANCELLED\') CHARACTER SET utf8mb4 DEFAULT \'PENDING\' NOT NULL COLLATE `utf8mb4_general_ci`, reservation_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, pickup_date DATE NOT NULL, pickup_time TIME DEFAULT NULL, cancellation_reason TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_reservations_client (client_id), INDEX idx_reservations_commerce (commerce_id), INDEX idx_reservations_status (status), UNIQUE INDEX reservation_number (reservation_number), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservation_items (id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, offer_id INT NOT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, subtotal NUMERIC(10, 2) NOT NULL, INDEX idx_items_offer (offer_id), INDEX idx_items_reservation (reservation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, commerce_id INT NOT NULL, reservation_id INT NOT NULL, rating TINYINT NOT NULL, comment TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_reviews_client (client_id), INDEX idx_reviews_commerce (commerce_id), UNIQUE INDEX reservation_id (reservation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT `fk_offers_category` FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT `fk_offers_commerce` FOREIGN KEY (commerce_id) REFERENCES commerces (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT `fk_reservations_client` FOREIGN KEY (client_id) REFERENCES users (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT `fk_reservations_commerce` FOREIGN KEY (commerce_id) REFERENCES commerces (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE reservation_items ADD CONSTRAINT `fk_items_offer` FOREIGN KEY (offer_id) REFERENCES offers (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE reservation_items ADD CONSTRAINT `fk_items_reservation` FOREIGN KEY (reservation_id) REFERENCES reservations (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT `fk_reviews_client` FOREIGN KEY (client_id) REFERENCES users (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT `fk_reviews_commerce` FOREIGN KEY (commerce_id) REFERENCES commerces (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT `fk_reviews_reservation` FOREIGN KEY (reservation_id) REFERENCES reservations (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE commerces DROP FOREIGN KEY FK_DFD4704EA76ED395');
        $this->addSql('ALTER TABLE commerces ADD description TEXT DEFAULT NULL, ADD postal_code VARCHAR(20) DEFAULT NULL, ADD latitude NUMERIC(10, 7) DEFAULT NULL, ADD longitude NUMERIC(10, 7) DEFAULT NULL, ADD logo VARCHAR(255) DEFAULT NULL, ADD cover_image VARCHAR(255) DEFAULT NULL, ADD opening_hours JSON DEFAULT NULL, ADD rejection_reason TEXT DEFAULT NULL, CHANGE business_type business_type ENUM(\'Restaurant\', \'Boulangerie\', \'Cafe\', \'Patisserie\', \'Supermarche\', \'Autre\') NOT NULL, CHANGE verification_status verification_status ENUM(\'PENDING\', \'APPROVED\', \'REJECTED\') DEFAULT \'PENDING\' NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE commerces ADD CONSTRAINT `fk_commerces_user` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commerces RENAME INDEX uniq_dfd4704ea76ed395 TO user_id');
        $this->addSql('ALTER TABLE users ADD profile_image VARCHAR(255) DEFAULT NULL, CHANGE role role ENUM(\'ROLE_CLIENT\', \'ROLE_COMMERCE\', \'ROLE_ADMIN\') DEFAULT \'ROLE_CLIENT\' NOT NULL, CHANGE is_active is_active TINYINT DEFAULT 1 NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX email ON users (email)');
        $this->addSql('ALTER TABLE verification_codes DROP FOREIGN KEY FK_7B56601DA76ED395');
        $this->addSql('ALTER TABLE verification_codes CHANGE channel channel ENUM(\'EMAIL\', \'PHONE\') NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE verification_codes ADD CONSTRAINT `fk_vc_user` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE verification_codes RENAME INDEX idx_7b56601da76ed395 TO idx_vc_user');
    }
}
