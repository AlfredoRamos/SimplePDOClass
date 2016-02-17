<?php
/**
 * Simple PDO Class - Customers example class
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @link https://github.com/AlfredoRamos/simple-pdo-class
 * @copyright Copyright (c) 2013 Alfredo Ramos
 * @licence GNU GPL-3.0+
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once __DIR__ . '/../PDODb/autoload.php';

class Customer {
	
	private $db;
	
	public function __construct() {
		$this->db = \AlfredoRamos\PDODb::instance();
		
		if (!$this->table_exists()) {
			$this->create_table();
		}
		
		if (!$this->initial_data_exist()) {
			$this->set_initial_data();
		}
		
	}
	
	public function table_exists() {
		
		$sql = 'SHOW TABLES LIKE "' . $this->db->prefix . 'customers"';
		$this->db->query($sql);
		$this->db->fetch();
		$row = $this->db->rowCount();
		
		return ($row > 0);
		
	}
	
	public function create_table() {
		
		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->db->prefix . 'customers (
					customer_id int(11) NOT NULL AUTO_INCREMENT,
					contact_name varchar(50) COLLATE utf8_unicode_ci NOT NULL,
					postal_address text COLLATE utf8_unicode_ci NOT NULL,
					city varchar(50) COLLATE utf8_unicode_ci NOT NULL,
					country varchar(50) COLLATE utf8_unicode_ci NOT NULL,
					postal_code varchar(15) COLLATE utf8_unicode_ci NOT NULL,
					created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					updated_at TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					deleted_at TIMESTAMP NULL DEFAULT NULL,
					PRIMARY KEY (customer_id)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;';
		$this->db->query($sql);
		$this->db->execute();
		
	}
	
	public function initial_data_exist() {
		
		// Just for testing
		$sql = 'SELECT COUNT(customer_id) AS total_rows
				FROM ' . $this->db->prefix . 'customers';
		$this->db->query($sql);
		$rows = $this->db->fetchField('total_rows');
		
		return ($rows >= 5);
		
	}
	
	public function set_initial_data() {
		
		// Start transaction
		$this->db->beginTransaction();
		
		$sql = 'INSERT INTO ' . $this->db->prefix . 'customers (
					contact_name,
					postal_address,
					city,
					country,
					postal_code
				) VALUES (
					:contact_name,
					:postal_address,
					:city,
					:country,
					:postal_code
				)';
		$this->db->query($sql);
		
		$this->db->bindArray([
			':contact_name'		=> 'Thomas Hardy',
			':postal_address'	=> '120 Hanover Sq.',
			':city'				=> 'London',
			':country'			=> 'United Kingdom',
			':postal_code'		=> 'WA1 1DP'
		]);
		$this->db->execute();
		
		$this->db->bindArray([
			':contact_name'		=> 'Christina Berglund',
			':postal_address'	=> 'Berguvsvägen 8',
			':city'				=> 'Luleå',
			':country'			=> 'Sweden',
			':postal_code'		=> 'S-958 22'
		]);
		$this->db->execute();
		
		$this->db->bindArray([
			':contact_name'		=> 'Ana Ramos',
			':postal_address'	=> 'Avda. de la Constitución 2222',
			':city'				=> 'México D.F.',
			':country'			=> 'Mexico',
			':postal_code'		=> '05021'
		]);
		$this->db->execute();
		
		$this->db->bindArray([
			':contact_name'		=> 'Howard Snyder',
			':postal_address'	=> '2732 Baker Blvd.',
			':city'				=> 'Eugene',
			':country'			=> 'United States of America',
			':postal_code'		=> '97403'
		]);
		$this->db->execute();
		
		$this->db->bindArray([
			':contact_name'		=> 'Renate Messner',
			':postal_address'	=> 'Magazinweg 7',
			':city'				=> 'Frankfurt a.M.',
			':country'			=> 'Germany',
			':postal_code'		=> '60528'
		]);
		$this->db->execute();
		
		$last_insert_id = (int) $this->db->lastInsertId();
		
		// End transaction
		$this->db->endTransaction();

	}
	
	public function get_raw_data() {
		
		$sql = 'SELECT customer_id, contact_name, postal_address, city, country, postal_code, created_at, updated_at, deleted_at
				FROM ' . $this->db->prefix . 'customers';
		$this->db->query($sql);
		
		return $this->db->fetchAll();
		
	}
	
	public function get_customers() {
		$sql = 'SELECT customer_id, contact_name, postal_address, city, country, postal_code
				FROM ' . $this->db->prefix . 'customers WHERE deleted_at IS NULL';
		$this->db->query($sql);
		
		return $this->db->fetchAll();
	}
	
	public function get_deleted_customers() {
		$sql = 'SELECT customer_id, contact_name, postal_address, city, country, postal_code
				FROM ' . $this->db->prefix . 'customers WHERE deleted_at IS NOT NULL';
		$this->db->query($sql);
		
		return $this->db->fetchAll();
	}
	
	public function delete_customer($customer_id) {
		$sql = 'UPDATE ' . $this->db->prefix . 'customers
				SET deleted_at = CURRENT_TIMESTAMP WHERE customer_id = :customer_id';
		$this->db->query($sql);
		$this->db->bind(':customer_id', $customer_id);
		$this->db->execute();
		
		$this->redirect();
	}
	
	public function restore_customer($customer_id) {
		$sql = 'UPDATE ' . $this->db->prefix . 'customers
				SET deleted_at = NULL WHERE customer_id = :customer_id';
		$this->db->query($sql);
		$this->db->bind(':customer_id', $customer_id);
		$this->db->execute();
		
		$this->redirect();
	}
	
	public function redirect($uri = '') {		
		header('Location: ' . $this->get_url() . $uri);
		exit;
	}
	
	public function get_url() {
		$page_url = (@$_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		$page_url .= $_SERVER['SERVER_NAME'];
		$page_url .= ($_SERVER['SERVER_PORT'] != '80') ? ':'.$_SERVER['SERVER_PORT'] : '';
		$page_url .= $_SERVER['REQUEST_URI'];
		
		$url = parse_url($page_url);
		$clean_url = $url['scheme'] . '://' . $url['host'] . $url['path'];
		
		return $clean_url;
	}
}