<?php

class QRGenerator {
    
    public static function generateQRCode($data, $options = []) {
        $size = $options['size'] ?? 300;
        $margin = $options['margin'] ?? 10;
        $darkColor = $options['darkColor'] ?? '#000000';
        $lightColor = $options['lightColor'] ?? '#ffffff';
        
        // Create image
        $image = imagecreate($size, $size);
        
        // Convert hex colors to RGB
        $dark = self::hexToRgb($darkColor);
        $light = self::hexToRgb($lightColor);
        
        // Allocate colors
        $lightColorResource = imagecolorallocate($image, $light[0], $light[1], $light[2]);
        $darkColorResource = imagecolorallocate($image, $dark[0], $dark[1], $dark[2]);
        
        // Fill background
        imagefill($image, 0, 0, $lightColorResource);
        
        // Generate QR pattern (simplified)
        $matrix = self::generateMatrix($data, $size - 2 * $margin);
        
        // Draw QR code
        $moduleSize = intval(($size - 2 * $margin) / count($matrix));
        
        for ($row = 0; $row < count($matrix); $row++) {
            for ($col = 0; $col < count($matrix[$row]); $col++) {
                if ($matrix[$row][$col]) {
                    $x1 = $margin + $col * $moduleSize;
                    $y1 = $margin + $row * $moduleSize;
                    $x2 = $x1 + $moduleSize - 1;
                    $y2 = $y1 + $moduleSize - 1;
                    
                    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $darkColorResource);
                }
            }
        }
        
        // Output as PNG
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        
        return $imageData;
    }
    
    public static function generateSVG($data, $options = []) {
        $size = $options['size'] ?? 300;
        $margin = $options['margin'] ?? 10;
        $darkColor = $options['darkColor'] ?? '#000000';
        $lightColor = $options['lightColor'] ?? '#ffffff';
        
        $matrix = self::generateMatrix($data, $size - 2 * $margin);
        $moduleSize = ($size - 2 * $margin) / count($matrix);
        
        $svg = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="' . $lightColor . '"/>';
        
        for ($row = 0; $row < count($matrix); $row++) {
            for ($col = 0; $col < count($matrix[$row]); $col++) {
                if ($matrix[$row][$col]) {
                    $x = $margin + $col * $moduleSize;
                    $y = $margin + $row * $moduleSize;
                    $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $moduleSize . '" height="' . $moduleSize . '" fill="' . $darkColor . '"/>';
                }
            }
        }
        
        $svg .= '</svg>';
        return $svg;
    }
    
    private static function generateMatrix($data, $size) {
        // Simplified QR code matrix generation
        // In production, use a proper QR library like chillerlan/php-qrcode
        
        $gridSize = 25; // Standard QR code size
        $matrix = array_fill(0, $gridSize, array_fill(0, $gridSize, false));
        
        // Add finder patterns (corners)
        self::addFinderPattern($matrix, 0, 0);
        self::addFinderPattern($matrix, 0, $gridSize - 7);
        self::addFinderPattern($matrix, $gridSize - 7, 0);
        
        // Add timing patterns
        for ($i = 8; $i < $gridSize - 8; $i++) {
            $matrix[6][$i] = ($i % 2 === 0);
            $matrix[$i][6] = ($i % 2 === 0);
        }
        
        // Add data (simplified pattern based on data hash)
        $hash = md5($data);
        for ($i = 9; $i < $gridSize - 9; $i++) {
            for ($j = 9; $j < $gridSize - 9; $j++) {
                $matrix[$i][$j] = (ord($hash[($i + $j) % strlen($hash)]) % 2 === 0);
            }
        }
        
        return $matrix;
    }
    
    private static function addFinderPattern(&$matrix, $row, $col) {
        $pattern = [
            [1,1,1,1,1,1,1],
            [1,0,0,0,0,0,1],
            [1,0,1,1,1,0,1],
            [1,0,1,1,1,0,1],
            [1,0,1,1,1,0,1],
            [1,0,0,0,0,0,1],
            [1,1,1,1,1,1,1]
        ];
        
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if ($row + $i < count($matrix) && $col + $j < count($matrix[0])) {
                    $matrix[$row + $i][$col + $j] = $pattern[$i][$j];
                }
            }
        }
    }
    
    private static function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }
}