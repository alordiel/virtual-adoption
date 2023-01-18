<?php
/**
 * Create database tables for the subscriptions and the transactions
 * Should be executed only when the plugin is activated
 *
 * @return void
 */
function va_create_subscription_tables() {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	global $wpdb;
	$subscriptions_table_name   = $wpdb->prefix . 'va_subscriptions';
	$transactions_table_name    = $wpdb->prefix . 'va_transactions';
	$subscriptions_table_create = "CREATE TABLE $subscriptions_table_name (
    								`ID` INT(10) NOT NULL AUTO_INCREMENT ,
    								`user_id` INT(16) NOT NULL ,
    								`sponsored_animal_id` INT(16) NOT NULL ,
    								`amount` INT NOT NULL ,
    								`currency` VARCHAR(126) NOT NULL,
    								`status` VARCHAR(255) NOT NULL ,
    								`period_type` VARCHAR(255) NOT NULL ,
    								`completed_cycles` INT NOT NULL DEFAULT '1' ,
    								`start_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    								`next_due` DATETIME NOT NULL ,
    								`post_id` INT(16) NOT NULL ,
    								`email_for_updates` VARCHAR(255) NOT NULL,
    								PRIMARY KEY (`ID`)
                   				) ENGINE = InnoDB;";
	$transactions_table_create  = "CREATE TABLE $transactions_table_name (
    								`ID` INT NOT NULL AUTO_INCREMENT ,
    								`subscription_id` INT NOT NULL ,
    								`payment_method` VARCHAR(255) NOT NULL ,
    								`payment_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    								`transaction_id` TEXT NOT NULL ,
    								`payment_status` VARCHAR(255) NOT NULL ,
    								`currency` VARCHAR(255) NOT NULL , PRIMARY KEY (`ID`)
                   				) ENGINE = InnoDB; ";
	maybe_create_table( $subscriptions_table_name, $subscriptions_table_create );
	maybe_create_table( $transactions_table_name, $transactions_table_create );
}
