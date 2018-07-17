<?php
namespace Asmpkg\Boleto\Utilitario;


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


    /**
     * @param null $valor
     * @return bool|mixed
     */
    public function formatarValor($valor)
    {
        return str_replace(['.',',',' '], "", $valor);
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

        while($i <= strlen($numero)-1){
            $soma += ($numero{$i} * $fator);
            $fator = $fator >= 9? 2:($fator+1);
            $i++;
        }

        return in_array(($soma % 11), ['0','10'])?'0':11 - ($soma % 11);
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

        while($i <= strlen($numero)-1){
            $resultado = $numero{$i} * $multiplicador;
            $soma += $resultado > 9 ? ($resultado - 9) : $resultado;
            $multiplicador = $multiplicador == 2 ? 1 : 2;
            $i++;
        }

        return in_array(($soma % 10), ['0'])?'0':10 - ($soma % 10);
    }

    public  function removerAcentos($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?', 'ç', 'Ç', "-","'" );
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o','c','C',"", " " );
        return str_replace($a, $b, $str);
    }

    /*Campos Numéricos (“Picture 9”)
    • Alinhamento: sempre à direita, preenchido com zeros à esquerda, sem máscara de edição;
    • Não utilizados: preencher com zeros.
    */
    public  function picture_9($palavra,$limite){

        $var=str_pad(preg_replace("/[^0-9]/", "", $palavra), $limite, "0", STR_PAD_LEFT);
        return $var;
    }

    /*
    Campos Alfanuméricos (“Picture X”)
    • Alinhamento: sempre à esquerda, preenchido com brancos à direita;
    • Não utilizados: preencher com brancos;
    • Caracteres: maiúsculos, sem acentuação, sem ‘ç’, sem caracteres especiais.
    */

    public  function picture_x( $palavra, $limite ){
        $palavra = $this->removerAcentos( $palavra );

        if( strlen( $palavra ) >= $limite )
            $palavra = substr( $palavra, 0, $limite );

        $var = str_pad( $palavra, $limite, " ", STR_PAD_RIGHT );
        $var = strtoupper( $var );// converte em letra maiuscula

        return $var;
    }

    public  function complementoRegistro($int,$tipo)
    {
        if($tipo == "zeros")
        {
            $space = '';
            for($i = 1; $i <= $int; $i++)
            {
                $space .= '0';
            }
        }
        else if($tipo == "brancos")
        {
            $space = '';
            for($i = 1; $i <= $int; $i++)
            {
                $space .= ' ';
            }
        }

        return $space;
    }


}