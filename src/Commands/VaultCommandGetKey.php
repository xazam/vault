<?php

namespace Azedan\Vault\Commands;

use Violuke\Vault\ServiceFactory as VaultFactory;
use Psecio\SecureDotenv\Parser;

class VaultCommandGetKey extends VaultCommand {

  private $argsErrorMessge;

  public function __construct($cwd, array $args) {
    $argsErrorMessge = "Erreur nombre d'arguments invlide." . PHP_EOL .
        " Utilisation: vault_get_key <env>";
    parent::__construct($cwd, $args, 2, $argsErrorMessge);

  }

  public function execute() {
    parent::execute();

    $env = $this->args[1];

    $dataService = $this->vaultFactory->get('data');
    $response = $dataService->get('secret/data/projects/' . getenv('VAULT_PROJECT'));
    $stream = $response->getBody();
    $stream->rewind();
    $json = json_decode($stream->getContents());

    if (! empty($json->data->data->{"secure_dotenv_key_" . $env})) {
      $key = $json->data->data->{"secure_dotenv_key_" . $env};
      $this->climate->blue($key);
    }
  }

}
