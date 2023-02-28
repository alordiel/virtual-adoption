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
	$subscriptions_table_create = "CREATE TABLE $subscriptions_table_name (
    								`ID` INT(10) NOT NULL AUTO_INCREMENT ,
    								`user_id` INT(16) NOT NULL ,
    								`sponsored_animal_id` INT(16) NOT NULL ,
    								`subscription_plan_id` VARCHAR(255) NOT NULL ,
    								`paypal_id` VARCHAR(255) NOT NULL,
    								`amount` INT NOT NULL ,
    								`currency` VARCHAR(126) NOT NULL,
    								`status` VARCHAR(255) NOT NULL ,
    								`period_type` VARCHAR(255) NOT NULL ,
    								`completed_cycles` INT NOT NULL DEFAULT '1' ,
    								`start_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    								`cancellation_date` DATETIME NULL DEFAULT NULL,
    								`next_due` DATETIME NOT NULL ,
    								`post_id` INT(16) NOT NULL ,
    								`email_for_updates` VARCHAR(255) NOT NULL,
    								PRIMARY KEY (`ID`)
                   				) ENGINE = InnoDB;";

	maybe_create_table( $subscriptions_table_name, $subscriptions_table_create );
}
