<?php

namespace Asmpkg\Boleto\Banco;

use Asmpkg\Boleto\Utilitario\Utilitario;
use Asmpkg\Boleto\BoletoInterface;

class Santander extends Utilitario implements BoletoInterface
{

    const BANCO =   "033";
    const MOEDA =   9;

    private $nossoNumero        =   null;
    private $codigoCedente      =   null;
    private $carteira           =   null;
    private $dvCodigoBarras     =   null;
    private $fatorVencimento    =   "0000";
    private $valor              =   "0,00";

    public function __construct()
    {
        return $this;
    }

    public function codigoBanco()
    {
        return static::BANCO;
    }

    /*
     * Número do PSK(Código do Cliente)
     */
    public function codigoCedente($codigoCedente)
    {
        $this->codigoCedente    =   $codigoCedente;
        return $this;
    }

    /*
     * Tipo de Modalidade Carteira
     * 101- Cobrança Simples Rápida COM Registro
     * 102- Cobrança simples SEM Registro
     * 201- Penhor
     */
    public function carteira($carteira)
    {
        $this->carteira =   $carteira;
        return $this;
    }

    /*
     * Para o cálculo, utilizar módulo 11, peso 2 a 9
     * Composição do Nosso Número:
     * NNNNNNNNNNNN D
     * N = Faixa seqüencial de 000000000001 a 999999999999
     * D = = Dígito de controle
     */
    public function nossoNumero($nossoNumero)
    {
        $nossoNumero    =   str_pad($nossoNumero, 12, "0", STR_PAD_LEFT);
        $dv =   $this->modulo11($nossoNumero);

        $this->nossoNumero  =   $nossoNumero.$dv;

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


    public function linhaDigitavel()
    {
        return $this->primeiroCampo().$this->segundoCampo().$this->terceiroCampo().$this->quartoCampo().$this->quintoCampo();
    }

    public function codigoBarras()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;
        //Fixo 9 e 00000 IOF fixo 0
        return $banco.$moeda.$this->dvCodigoBarras.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT)."9".$this->codigoCedente.$this->nossoNumero."0".$this->carteira;
    }


    /**
     * Referente ao primeiro Campo da linha digitavel da posicao 01 a 10
     */
    private function primeiroCampo()
    {

        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;

        //Fixo “9”
        $primeiroCampo  =   $banco.$moeda."9".substr($this->codigoCedente, 0, 4);

        $dv =   $this->modulo10($primeiroCampo);

        $linhaDigitavel =   $primeiroCampo.$dv;
        return  $linhaDigitavel;
    }
    /**
     * Referente ao segundo Campo da linha digitavel da posicao 11 a 21
     */
    private function segundoCampo()
    {
        $segundoCampo   =   substr($this->codigoCedente, 4, strlen($this->codigoCedente)).substr($this->nossoNumero, 0, 7);
        $dv =   $this->modulo10($segundoCampo);

        $linhaDigitavel =   $segundoCampo.$dv;
        return $linhaDigitavel;
    }
    /**
     * Referente ao terceiro Campo da linha digitavel da posicao 22 a 32
     */
    private function terceiroCampo()
    {
        /*
         * Foi fixado 0 (ZERO) entretanto, para Seguradoras (Se 7% informar 7, limitado a 9%)
         */
        $terceiroCampo  =   substr($this->nossoNumero, 7, strlen($this->nossoNumero))."0".$this->carteira;
        $dv =   $this->modulo10($terceiroCampo);

        $linhaDigitavel =   $terceiroCampo.$dv;
        return $linhaDigitavel;
    }
    /**
     * Referente ao quarto Campo da linha digitavel da posicao 33
     */
    private function quartoCampo()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;
        /*
         * Foi fixado 0 (ZERO) entretanto, para Seguradoras (Se 7% informar 7, limitado a 9%)
         */
        $quartoCampo    =   $banco.$moeda.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT)."9".$this->codigoCedente.$this->nossoNumero."0".$this->carteira;
        $dv = $this->dvCodigoBarras    =   $this->modulo11($quartoCampo);
        return $dv;
    }
    /**
     * Referente ao quinto Campo da linha digitavel da posicao 34 a 47
     */
    private function quintoCampo()
    {
        return $this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT);
    }

}