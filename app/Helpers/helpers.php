<?php

if (!function_exists('sanitize_name')) {
    function sanitize_name(string $name): string
    {
        // Remove special characters except spaces
        $cleaned = preg_replace('/[^A-Za-z\s]/', '', $name);
        
        // Trim whitespace and capitalize first letter
        return ucfirst(strtolower(trim($cleaned)));
    }
}
