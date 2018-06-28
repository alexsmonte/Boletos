<?php
namespace Asmpkg\Boleto\Banco;

use Asmpkg\Boleto\Utilitario\Utilitario;
use Asmpkg\Boleto\BoletoInterface;

class Itau extends Utilitario implements BoletoInterface
{
    const BANCO =   "341";
    const MOEDA =   9;

    private $nossoNumero        =   null;
    private $codigoCedente      =   null;
    private $carteira           =   null;
    private $dvCodigoBarras     =   null;
    private $fatorVencimento    =   "0000";
    private $valor;

    public function __construct()
    {
        return $this;
    }

    public function codigoBanco()
    {
        return static::BANCO;
    }

    public function carteira($carteira)
    {
        $this->carteira =   $carteira;
        return $this;
    }

    public function codigoCedente($codigoCedente)
    {
        $this->codigoCedente    =   $codigoCedente;
        return $this;
    }

    public function valor($valor)
    {
        $this->valor    =   $valor;
        return $this;
    }

    public function vencimento($vencimento)
    {
        $this->fatorVencimento =   $this->fatorVencimento($vencimento);
    }

    public function nossoNumero($nossoNumero)
    {
        // TODO: Implement nossoNumero() method.
    }

    public function linhaDigitavel()
    {
        // TODO: Implement linhaDigitavel() method.
    }
    public function codigoBarras()
    {
        // TODO: Implement codigoBarras() method.
    }

    private function primeiroCampo()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;

        $primeiroGrupo  =   $banco.$moeda.$this->carteira.substr($this->nossoNumero, 0, 2);
    }

}