<?php
namespace Asmpkg\Boleto;
use ReflectionClass;

class Boleto
{

    const VERSION = '0.1';

    private $banco;
    private $vencimento;
    private $valor;

    public function __construct ($banco)
    {
        $namespace      =    new ReflectionClass("Asmpkg\\Boleto\\Banco\\".$banco);
        $this->banco    =    $namespace->newInstance();
    }

    public function conta($conta)
    {
        $this->banco->conta($conta);
        return $this;
    }

    public function agencia($agencia)
    {
        $this->banco->agencia($agencia);
        return $this;
    }

    public function codigoCedente($codigoCedente)
    {
        $this->banco->codigoCedente($codigoCedente);
        return $this;
    }

    public function carteira($carteira)
    {
        $this->banco->carteira($carteira);
        return $this;
    }

    public function nossoNumero($nossoNumero)
    {
        $this->banco->nossoNumero($nossoNumero);
        return $this;
    }

    public function valor($valor)
    {
        $this->valor    =   $valor;
        $this->banco->valor($valor);
        return $this;
    }

    public function vencimento($vencimento)
    {
        $this->vencimento   =   $vencimento;
        $this->banco->vencimento($vencimento);
        return $this;
    }

    public function linhaDigitavel(){
        return $this->banco->linhaDigitavel();
    }

    public function codigoBarras()
    {
        return $this->banco->codigoBarras();
    }
}