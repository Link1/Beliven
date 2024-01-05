<?php
/*
Plugin Name: Beliven-test
Description: Technical test for Beliven
Version: 1.0
Author: Marco Link1 Grossi
*/

// carico tutti i files PHP presenti nella directory /inc/ 
// ogni file contiene determinate funzioni - in questo modo diviene semplice trovare il giusto file.
// Inoltre, in caso di plugins piu complicati, permette di ottimizzare le directories

foreach(glob( plugin_dir_path( __FILE__ ) . 'inc/*.php') as $filename) { include $filename; } 

