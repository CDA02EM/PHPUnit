<?php
namespace App;
    class Calculator {
        public function add($a, $b) {
        try {
            if (!is_numeric($a) || !is_numeric($b)) {
                throw new \InvalidArgumentException('Mauvais nombre');
            }
            return $a + $b;
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }

    public function divisor($a, $b) {
        try {
            if ($b === 0) {
                throw new \DivisionByZeroError('Impossible de diviser par zéro.');
            }
            $result = $a / $b;
            return round($result, 2);
        } catch (\DivisionByZeroError) {
            throw new \Exception('Impossible de diviser par zéro.');
        }
    }
}