<?php echo View::factory('migrations/_header' ); ?>

<h1>Install Migrations</h1>

<div>To install the migrations db table, please run:</div>
<pre>$ ./minion migration install</pre>

<br>

<div>Or execute the following SQL manually:</div>

<pre>
CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `updated_at` datetime default NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`)
);
</pre>

<?php echo View::factory('migrations/_footer' ); ?>