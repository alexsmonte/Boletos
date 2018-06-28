<?php
namespace Asmpkg\Boleto\Banco;

use Asmpkg\Boleto\Utilitario\Utilitario;
use Asmpkg\Boleto\BoletoInterface;

class Itau extends Utilitario implements BoletoInterface
{
    const BANCO =   "341";
    const MOEDA =   9;

    public $nossoNumero        =   null;
    private $codigoCedente      =   null;
    private $carteira           =   null;
    private $dvCodigoBarras     =   null;
    private $fatorVencimento    =   "0000";
    private $valor;
    private $agencia;
    private $conta;

    public function __construct()
    {
        return $this;
    }

    public function codigoBanco()
    {
        return static::BANCO;
    }

    public function agencia($agencia)
    {
        $this->agencia  =   $agencia;
        return $this;
    }

    public function conta($conta)
    {
        $this->conta  =   $conta;
        return $this;
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
        $this->nossoNumero  =   str_pad($nossoNumero, 8, "0", STR_PAD_LEFT);

        $numero = $this->agencia.$this->conta.$this->carteira.$nossoNumero;
        $this->dvDacNossoNumero    =   $this->modulo10($numero);
        return $this;
    }

    public function linhaDigitavel()
    {
        return $this->primeiroCampo().$this->segundoCampo().$this->terceiroCampo().$this->quartoCampo().$this->quintoCampo();
    }


    public function codigoBarras()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;

        $codigoBarra    =   $banco.$moeda.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT).$this->carteira.$this->nossoNumero.$this->dvDacNossoNumero.$this->agencia.$this->conta.$this->modulo10($this->agencia.$this->conta).'000';

        $dacCodigoBarra	=	$this->modulo11($codigoBarra);

        return $banco.$moeda.$dacCodigoBarra.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT).$this->carteira.$this->nossoNumero.$this->dvDacNossoNumero.$this->agencia.$this->conta.$this->modulo10($this->agencia.$this->conta).'000';
    }

    private function primeiroCampo()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;

        $primeiroCampo  =   $banco.$moeda.$this->carteira.substr($this->nossoNumero,0,2);
        $dacPrimeiroCampo   =   $this->modulo10($primeiroCampo);

        $primeiroCampo  .= $dacPrimeiroCampo;

        return $primeiroCampo;
    }

    private function segundoCampo()
    {
        $segundoCampo	= substr($this->nossoNumero,2,6).$this->dvDacNossoNumero.substr($this->agencia,0, 3);

        $dacSegundoCampo   =   $this->modulo10($segundoCampo);

        return substr($this->nossoNumero,2,6).$this->dvDacNossoNumero.substr($this->agencia,0, 3).$dacSegundoCampo;
    }

    private function terceiroCampo()
    {
        $terceiroCampo	=	substr($this->agencia,3, strlen($this->agencia)-3).$this->conta.$this->modulo10($this->agencia.$this->conta).'000';

        $dacTerceiroCampo =   $this->modulo10($terceiroCampo);

        return $terceiroCampo.$dacTerceiroCampo;
    }

    private function quartoCampo()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;

        $codigoBarra    =   $banco.$moeda.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT).$this->carteira.$this->nossoNumero.$this->dvDacNossoNumero.$this->agencia.$this->conta.$this->modulo10($this->agencia.$this->conta).'000';

        return $this->modulo11($codigoBarra);
    }

    private function quintoCampo()
    {
        return $this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT);
    }

}