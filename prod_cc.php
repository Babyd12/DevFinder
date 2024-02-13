<?php

$symfonyScript = __DIR__ . '/bin/console'; 

// Exécutez la commande de cache clear
$command = 'php ' . $symfonyScript . ' cache:clear --env=prod --no-warmup';
exec($command, $output, $returnValue);

// Affichez la sortie (utile pour le débogage)
echo implode("\n", $output);

// Gérer le cas d'échec
if ($returnValue !== 0) {
    throw new \RuntimeException('La commande cache:clear a échoué avec le code ' . $returnValue);
}
