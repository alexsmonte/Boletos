<?php

namespace Asmpkg\Boleto\Banco;
use Asmpkg\Boleto\Utilitario\Utilitario;
use Asmpkg\Boleto\BoletoInterface;

class Bradesco extends Utilitario implements BoletoInterface
{
    const BANCO =   "237";
    const MOEDA =   9;

    public $nossoNumero        =   null;
    private $codigoCedente      =   null;
    private $carteira           =   null;
    private $dvCodigoBarras     =   null;
    private $fatorVencimento    =   "0000";
    private $valor              =   "0,00";
    public $agencia;
    public $conta;



    public function __construct()
    {
        return $this;
    }

    public function codigoBanco()
    {
        return static::BANCO;
    }

    /***
     * AGÊNCIA / CÓDIGO DO CEDENTE:
     * Deverá ser preenchido com a agência com 4(quatro caracteres) -
     * digito da agência / Conta de Cobrança com 7(sete) caracteres - Digito da Conta.
     * Ex. 9999-D/9999999-D
     *
     * @param $codigoCedente
     * @return $this
     */
    public function codigoCedente($codigoCedente)
    {
        $this->codigoCedente    =   $codigoCedente;
        return $this;
    }

    public function carteira($carteira)
    {
        $this->carteira =   $carteira;
        return $this;
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


    public function valor($valor)
    {
        $this->valor    =   $valor;
        return $this;
    }

    public function nossoNumero($nossoNumero)
    {
        $this->nossoNumero  =   str_pad($nossoNumero, 11, "0", STR_PAD_LEFT);
        return $this;
    }

    /***
     *
     *
     * CÁLCULO DO MÓDULO 11
     * O modulo 11, de um número é calculado multiplicando cada algarismo, pela seqüência de multiplicadores 2,3,4,5,6,7,8,9,2,3, ... posicionados da direita para a esquerda.
     * A soma dos algarismos do produto é dividida por 11 e o DV (dígito verificador) será a diferença entre o divisor ( 11 ) e o resto da divisão:
     * DV = 11 - (resto da divisão)
     * Observação: quando o resto da divisão for 0 (zero) ou 10 (dez), o DV calculado é o 0 (zero).
     *
     * EXEMPLO
     * calcular o DV módulo 11 da seguinte seqüência de números: 018 520 0005
     * A fórmula do cálculo é:
     * 1. Multiplicação pela seqüência 2,3,4,5,6,7,8,9,2,3, ... da direita para a esquerda.
     * 2. Soma dos dígitos do produto
     * 0 + 2 + 72 + 40 + 14 + 0 + 0 + 0 + 0 + 10 = 138
     *
     * Observação:
     * Neste caso os resultados deverão ser somados integralmente.
     *
     * 3. Divisão do resultado da soma acima por 11
     * 138 : 11 = 12 e o resto da divisão = 6
     * DV = 11 - (resto da divisão), portando 11 - 6 = 5
     *
     *
     * @param $numero
     * @return int
     */
    public function modulo11($numero, $modulo = 9)
    {
        $soma   =   0;
        $fator  =   2;
        $numero =   strrev($numero);
        $i  = 0;
        while($i <= strlen($numero)-1){
            $soma += ($numero{$i} * $fator);
            $fator = $fator >= $modulo? 2:($fator+1);
            $i++;
        }

        if ($modulo==9)
            return in_array(($soma % 11), ['0','10'])?'0':11 - ($soma % 11);

        $restoModulo7   =   in_array(($soma % 11), ['0','10'])?'0':11 - ($soma % 11);

        if ($restoModulo7 == 10)
            return "P";

        return $restoModulo7;
    }

    public function modulo10($numero)
    {
        $multiplicador = 2;
        $resultado = 0;
        $numero =   strrev($numero);
        $soma = 0;
        $i  = 0;
        while($i <= strlen($numero)-1){
            $resultado = $numero{$i} * $multiplicador;
            $soma += $resultado > 9 ? ($resultado - 9) : $resultado;
            $multiplicador = $multiplicador == 2 ? 1 : 2;
            $i++;
        }

        return in_array(($soma % 10), ['0'])?'0':10 - ($soma % 10);
    }

    public function digitoVerificadoNossoNumero($numero, $modulo = 7)
    {

        //echo $numero;

        $soma   =   0;
        $fator  =   2;
        $numero =   strrev($numero);
        $i  = 0;
        while($i <= strlen($numero)-1){
            $soma += ($numero{$i} * $fator);
            $fator = $fator >= $modulo? 2:($fator+1);
            $i++;
        }
        $restoModulo7   =   ($soma % 11);

        $restoModulo7   =   11 - $restoModulo7;

        if ($restoModulo7 == 10)
            return "P";

        if ($restoModulo7 == 11)
            return "0";

        return $restoModulo7;
    }


    public function digitoVerificadoCodigoBarras($numero, $modulo = 9)
    {
        $soma   =   0;
        $fator  =   2;
        $numero =   strrev($numero);
        $i  = 0;
        while($i <= strlen($numero)-1){
            $soma += ($numero{$i} * $fator);
            $fator = $fator >= $modulo? 2:($fator+1);
            $i++;
        }

        if ($modulo==9)
            return in_array(($soma % 11), ['0','1','10'])?'1':11 - ($soma % 11);

        $restoModulo7   =   in_array(($soma % 11), ['0','10'])?'0':11 - ($soma % 11);

        if ($restoModulo7 == 10)
            return "P";

        return $restoModulo7;
    }


    public function vencimento($vencimento)
    {
        $this->fatorVencimento =   $this->fatorVencimento($vencimento);

    }

    public function campoLivre()
    {
        return str_pad($this->agencia, 4, "0", STR_PAD_LEFT).$this->carteira.$this->nossoNumero.str_pad($this->conta, 7, "0", STR_PAD_LEFT).'0';
    }

    /**
     * Referente ao primeiro grupo da linha digitavel da posicao 01 a 10
     */
    private function primeiroCampo()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;
        //Fixo “9”

        $primeiroGrupo  =   $banco.$moeda.substr($this->campoLivre(), 0, 5);

        $dv =   $this->modulo10($primeiroGrupo);
        $linhaDigitavel =   $primeiroGrupo.$dv;
        return  $linhaDigitavel;
    }

    private function segundoCampo()
    {
        $segundoCampo   =   substr($this->campoLivre(), 5, 10);
        $dv =   $this->modulo10($segundoCampo);
        $linhaDigitavel =   $segundoCampo.$dv;
        return  $linhaDigitavel;
    }

    private function terceiroCampo()
    {
        $terceiroCampo   =   substr($this->campoLivre(), 15, 10);
        $dv =   $this->modulo10($terceiroCampo);
        $linhaDigitavel =   $terceiroCampo.$dv;
        return  $linhaDigitavel;
    }

    private function quartoCampo()
    {
        $this->codigoBarras();

        return $this->dvCodigoBarras;
    }

    private function quintoCampo()
    {
        return $this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT);
    }

    public function codigoBarras()
    {
        $banco  =   static::BANCO;
        $moeda  =   static::MOEDA;

        $codigoBarras   =   $banco.$moeda.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT).$this->campoLivre();

        $dv     =   $this->dvCodigoBarras   =   $this->digitoVerificadoCodigoBarras($codigoBarras);

        return $banco.$moeda.$dv.$this->fatorVencimento.str_pad($this->formatarValor($this->valor), 10, "0", STR_PAD_LEFT).$this->campoLivre();

    }

    public function linhaDigitavel()
    {
        return $this->primeiroCampo().$this->segundoCampo().$this->terceiroCampo().$this->quartoCampo().$this->quintoCampo();
    }

}