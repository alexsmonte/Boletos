<?php

namespace Asmpkg\Boleto\Banco;

use Asmpkg\Boleto\Utilitario\Utilitario;
use Asmpkg\Boleto\BoletoInterface;

class Bb extends Utilitario implements BoletoInterface
{
    const BANCO =   "001";
    const MOEDA =   9;

    private $nossoNumero        =   null;
    private $codigoCedente      =   null;
    private $carteira           =   null;
    private $dvCodigoBarras     =   null;
    private $fatorVencimento;
    private $valor;
    private $convenio;

    public function __construct()
    {
        return $this;
    }

    public function convenio($convenio)
    {
        $this->convenio    =   $convenio;
        return $this;
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
        return $this->primeiroGrupo().$this->segundoGrupo().$this->terceiroGrupo().$this->quartoGrupo().$this->quintoGrupo();
    }

    public function codigoBarras()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;
        //Fixo 9 e 00000 IOF fixo 0
        return $banco.$moeda.$this->dvCodigoBarras.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT)."9".$this->codigoCedente.$this->nossoNumero."0".$this->carteira;
    }

}