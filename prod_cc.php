<?php
 $symfonyScript = __DIR__ . '/../../bin/console';  // Modifiez le chemin selon votre structure de projet

 // Exécutez la commande de cache clear
 $command = 'php ' . $symfonyScript . ' cache:clear --env=prod --no-warmup';
 exec($command, $output, $returnValue);
