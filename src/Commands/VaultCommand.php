<?php


namespace Azedan\Vault\Commands;

use League\CLImate\CLImate;
use Violuke\Vault\ServiceFactory as VaultFactory;

class VaultCommand {

  protected $args;

  protected $cwd;

  private $argsCount;

  protected $climate;

  protected $vaultFactory;

  protected $argsErrorMessage;

  public function __construct($cwd, array $args, $argsCount, $arggErrorMessage = "") {
    $this->cwd = $cwd;
    $this->args = $args;
    $this->argsCount = $argsCount;
    $this->argsErrorMessage = $arggErrorMessage;
    $this->climate = new CLImate();
    $vaultOptions['headers']['X-Vault-Token'] = getenv('VAULT_TOKEN');
    $this->vaultFactory = new VaultFactory($vaultOptions);
  }

  public function execute() {
      if (!$this->checkRequirements()) {
        exit(0);
      }
  }

  public function checkRequirements(){
    $error = FALSE;
    if(count($this->args) != $this->argsCount){
      $this->climate->to('error')->red($this->argsErrorMessage);
      $error = TRUE;
    }

    if (empty(getenv('VAULT_TOKEN'))) {
      $climate->to('error')->red(
        'You must set VAULT_TOKEN env var'
      );
      $error = TRUE;
    }

    if (empty(getenv('VAULT_PROJECT'))) {
      $climate->to('error')->red(
        'You must set VAULT_PROJECT env var'
      );
      $error = TRUE;
    }
    return !$error;
  }

}
