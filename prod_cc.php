<?php
  $command = 'php cache:clear --env=prod --no-warmup';
  exec($command, $output, $returnValue);
