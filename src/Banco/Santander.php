<?php

namespace Asmpkg\Boleto;
use Asmpkg\Boleto\Utilitario;


class Santander extends Utilitario implements BoletoInterface
{

    const BANCO =   "353";
    const MOEDA =   9;

    private $nossoNumero        =   null;
    private $codigoCedente      =   null;
    private $carteira           =   null;
    private $dvCodigoBarras     =   null;
    private $valor;
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
     * 101-Cobrança Simples Rápida COM Registro
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

    public function linhaDigitavel()
    {
        return $this->primeiroGrupo().$this->segundoGrupo().$this->terceiroGrupo().$this->quartoGrupo().$this->quintoGrupo();
    }

    public function codigoBarras()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;
        //Fixo 9 e 00000 IOF fixo 0
        return $banco.$moeda.$this->dvCodigoBarras.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT)."9".$this->codigoCedente."00000".$this->nossoNumero."0".$this->carteira;
    }


    /**
     * Referente ao primeiro grupo da linha digitavel da posicao 01 a 10
     */
    private function primeiroGrupo()
    {

        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;

        //Fixo “9”
        $primeiroGrupo  =   $banco.$moeda."9".substr($this->codigoCedente, 0, 4);

        $dv =   $this->modulo10($primeiroGrupo);

        $linhaDigitavel =   $primeiroGrupo.$dv;

        return  $linhaDigitavel;
    }
    /**
     * Referente ao segundo grupo da linha digitavel da posicao 11 a 21
     */
    private function segundoGrupo()
    {
        $segundoGrupo   =   substr($this->codigoCedente, 4, strlen($this->codigoCedente)).substr($this->nossoNumero, 0, 7);
        $dv =   $this->modulo10($segundoGrupo);

        $linhaDigitavel =   $segundoGrupo.$dv;
        return $linhaDigitavel;
    }
    /**
     * Referente ao terceiro grupo da linha digitavel da posicao 22 a 32
     */
    private function terceiroGrupo()
    {
        /*
         * Foi fixado 0 (ZERO) entretanto, para Seguradoras (Se 7% informar 7, limitado a 9%)
         */
        $terceiroGrupo  =   substr($this->nossoNumero, 7, strlen($this->nossoNumero))."0".$this->carteira;
        $dv =   $this->modulo10($terceiroGrupo);

        $linhaDigitavel =   $terceiroGrupo.$dv;
        return $linhaDigitavel;
    }
    /**
     * Referente ao quarto grupo da linha digitavel da posicao 33
     */
    private function quartoGrupo()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;
        /*
         * Foi fixado 0 (ZERO) entretanto, para Seguradoras (Se 7% informar 7, limitado a 9%)
         */
        $quartoGrupo    =   $banco.$moeda.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT)."9".$this->codigoCedente.$this->nossoNumero."0".$this->carteira;
        $dv = $this->dvCodigoBarras    =   $this->modulo11($quartoGrupo);
        return $dv;
    }
    /**
     * Referente ao quinto grupo da linha digitavel da posicao 34 a 47
     */
    private function quintoGrupo()
    {
        return $this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT);
    }

}