<?php


namespace Asmpkg\Boleto;


class Utilitario
{


    /***
     * O vencimento de um boleto bancário (bloqueto de cobrança) corresponde ao número de dias decorridos entre a
     * "data base" instituída pelo Banco Central do Brasil - BACEN e a "data de vencimento". A "data base" instituída
     * pelo BACEN é: 07/10/1997.
     *
     * Desta forma, um boleto bancário vencido em 31/12/2011 teria no campo "vencimento" os números: "5198". Os números
     * "5198" correspondem ao número de dias decorridos entre 07/10/1997 e 31/12/2011 (31/12/2011 - 07/10/1997 = 5198)
     */
    protected $data_base =   '1997-10-07';

    /**
     * @param $dataVencimento YYYY/MM/DD
     *
     * @return numeric
     */
    public function fatorVencimento($dataVencimento = null)
    {
        if (is_null($dataVencimento))
            return "0000";

        $vencimento = new \DateTime( date('Y-m-d', strtotime(str_replace("/","-", $dataVencimento))) );
        $data_base = new \DateTime( $this->data_base );
        $dias = $vencimento->diff( $data_base )->days;

        return $dias;
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
    public function modulo11($numero)
    {
        $soma   =   0;
        $fator  =   2;
        $numero =   strrev($numero);
        $i  = 0;

        while($i <= strlen($numero)){
            $soma += ($numero{$i} * $fator);
            $fator = $fator >= 9? 2:($fator+1);
            $i++;
        }

        return 11 - ($soma % 11);
    }

    /***
     * CÁLCULO DO MÓDULO 10
     *
     * O modulo 10, de um número é calculado multiplicando cada algarismo, pela seqüência de multiplicadores 2, 1, 2, 1, ... posicionados da direita para a esquerda.
     * A soma dos algarismos do produto é dividida por 10 e o DV (dígito verificador) será a diferença entre o divisor ( 10 ) e o resto da divisão:
     * DV = 10 - (resto da divisão)
     * Observação: quando o resto da divisão for 0 (zero), o DV calculado é o 0 (zero).
     *
     * EXEMPLO
     * calcular o DV módulo 10 da seguinte seqüência de números: 0123 006789-6.
     * A fórmula do cálculo é:
     * 1. Multiplicação pela seqüência 2, 1, 2, 1, ... da direita para a esquerda.
     * 2. Soma dos dígitos do produto
     * 0 + 1 + 4 + 3 + 0 + 0 + (1 + 2) + 7 + (1 + 6) + 9 + (1 + 2) = 37
     *
     * Observação: Cada dígito deverá ser somado individualmente.
     *
     * 3. Divisão do resultado da soma acima por 10
     * 37 : 10 = 3 e o resto da divisão = 7
     * DV = 10 - (resto da divisão), portando 10 - 7 = 3

     *
     * @param $numero
     * @return int
     */
    public function modulo10($numero)
    {
        $multiplicador = 2;
        $resultado = 0;
        $numero =   strrev($numero);
        $soma = 0;
        $i  = 0;

        while($i <= strlen($numero)){
            $resultado = $numero{$i} * $multiplicador;
            $soma += $resultado > 9 ? ($resultado - 9) : $resultado;
            $multiplicador = $multiplicador == 2 ? 1 : 2;
            $i++;
        }

        return 10 - ($soma % 10);
    }

}