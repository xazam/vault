<?php

namespace Azedan\Vault\Commands;

use Violuke\Vault\ServiceFactory as VaultFactory;
use Psecio\SecureDotenv\Parser;

class VaultCommandAdd extends VaultCommand {

  public function __construct($cwd, array $args) {
    $argsErrorMessge = "Erreur nombre d'arguments invlide." . PHP_EOL .
      " Utilisation: vault_add <env> <var_name> <var_value>";
    parent::__construct($cwd, $args, 4, $argsErrorMessge);
  }

  public function execute() {
    parent::execute();

    $env = $this->args[1];
    $var = $this->args[2];
    $val = $this->args[3];

    $dataService = $this->vaultFactory->get('data');
    $response = $dataService->get('secret/data/projects/' . getenv('VAULT_PROJECT'));
    $stream = $response->getBody();
    $stream->rewind();
    $json = json_decode($stream->getContents());
    if (! empty($json->data->data->{"secure_dotenv_key_" . $env})) {
      $key = $json->data->data->{"secure_dotenv_key_" . $env};
      $vaultFile = $this->cwd . "/config/vault/" . $env . "/.env";
      if(file_exists($vaultFile)){
        $parser = new Parser($key, $vaultFile);
        $parser->save($var, $val, True);
        $this->climate->green('[OK]');
      }
    }
  }

}
