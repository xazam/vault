<?php

namespace Azedan\Vault\Commands;

use Violuke\Vault\ServiceFactory as VaultFactory;
use Psecio\SecureDotenv\Parser;

class VaultCommandShow extends VaultCommand {

  private $argsErrorMessge;

  public function __construct($cwd, array $args) {
    $argsErrorMessge = "Erreur nombre d'arguments invlide." . PHP_EOL .
        " Utilisation: vault_show <env>";
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
      $vaultFile = $this->cwd . "/config/vault/" . $env . "/.env";
      if(file_exists($vaultFile)){
        $parser = new Parser($key, $vaultFile);
        $this->climate->green(
          var_export($parser->getContent(),TRUE)
        );
      }else{
        $this->climate->to('error')->red(
          'Vault file not found' . PHP_EOL . $vaultFile
        );
      }
    }else{
      $this->climate->to('error')->red(
        'Env master key not found for env "' . $env .'"'
      );
    }
  }

}
