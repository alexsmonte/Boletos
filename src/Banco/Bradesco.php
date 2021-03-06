<?php

namespace Asmpkg\Boleto\Banco;
use Asmpkg\Boleto\Utilitario\Utilitario;
use Asmpkg\Boleto\BoletoInterface;

class Bradesco extends Utilitario implements BoletoInterface
{
    const BANCO =   "237";
    const NOME_BANCO =   "BRADESCO";
    const MOEDA =   9;

    public $nossoNumero        =   null;
    public $codigoCedente      =   null;
    private $carteira           =   null;
    private $dvCodigoBarras     =   null;
    private $fatorVencimento    =   "0000";
    private $valor              =   "0,00";
    public $agencia;
    public $conta;
    public $digitoAgencia;
    public $digitoConta;



    public function __construct()
    {
        return $this;
    }

    public function codigoBanco()
    {
        return static::BANCO."-2";
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
    public function codigoCedente($codigoCedente = null)
    {
        $this->codigoCedente    =   $this->agencia."-".$this->digitoAgencia."/".$this->conta."-".$this->digitoConta;
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

    public function digitoAgencia($digitoAgencia)
    {
        $this->digitoAgencia    =   $digitoAgencia;
    }

    public function digitoConta($digitoConta)
    {
        $this->digitoConta    =   $digitoConta;
    }

    public function valor($valor)
    {
        $this->valor    =   $valor;
        return $this;
    }

    public function nossoNumero($nossoNumero)
    {
        $this->nossoNumero  =   str_pad($nossoNumero, 11, "0", STR_PAD_LEFT);
        return $this->nossoNumero."-".$this->digitoVerificadoNossoNumero($this->carteira.$this->nossoNumero);
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


    public function gerarArquivoRemessa($sequencial = '01', $teste = false)
    {
        $nomeArquivo    =   "CB".date("d").date("m").$sequencial;
        $extensao       =   ".REM";

        if($teste)
            $extensao       =   ".TST";

        return $nomeArquivo.$extensao;
    }

    public function header($codigoBeneficiario, $empresaBeneficiario, $numeroSequencial, $numeroLinha)
    {

        $banco      =   static::BANCO;
        $nomeBanco  =   static::NOME_BANCO;

        return "01REMESSA01".$this->picture_x("COBRANCA", 15).$this->picture_9($codigoBeneficiario, 20).$this->picture_x($empresaBeneficiario,30).$this->picture_9($banco,3).$this->picture_x($nomeBanco,15).date("d").date("m").date("y").$this->complementoRegistro(8,"brancos")."MX".$this->picture_9($numeroSequencial,7).$this->complementoRegistro(277,"brancos").$this->picture_9($numeroLinha,6).chr(13).chr(10);
    }

    public function transacao($dados, $numeroLinha)
    {
        $banco      =   static::BANCO;
        $transacao  =   "1";
        $transacao  .=   $this->complementoRegistro(5,"brancos");
        $transacao  .=   $this->complementoRegistro(1,"brancos");
        $transacao  .=   $this->complementoRegistro(5,"brancos");
        $transacao  .=   $this->complementoRegistro(7,"brancos");
        $transacao  .=   $this->complementoRegistro(1,"brancos");
        $transacao  .=   '0';

        $transacao .= $this->picture_9(trim($dados["carteira"]),3);                              // 022 a 024 -> Codigo da carteira
        $transacao .= $this->picture_9(substr(trim($dados["agencia"]), 0, -2),5);    // 025 a 029 -> Codigo da agencia cedente
        $transacao .= $this->picture_9(trim($dados["conta"]),7);                                 // 030 a 036 -> Conta corrente
        $transacao .= $this->picture_9(trim($dados["conta_dv"]),1);                              // 037 a 037 -> Digito da conta

        $transacao .= $this->picture_9(substr(trim($dados["numero_documento"]), 0, 9),25);   // 038 a 062 -> No. controle -> (livre e para uso da empresa - usei o num. docto )
        $transacao .= $this->picture_9(trim($dados["banco"]),3);                      // 063 a 065 -> Codigo do banco a ser debitado na camara de compensacao
        $transacao .= $this->picture_9(trim($dados["tipo_multa"]),1);                        // 066 a 066 -> Campo de multa. 2=percentual/1=em reais/0=sem multa
        $transacao .= $this->picture_9(trim($dados["valor_multa"]),4);                       // 067 a 070 -> Percentual ou valor da multa. Vide o tipo de multa no item acima.
        $transacao .= $this->picture_9(substr(trim($dados["nosso_numero"]), 0, 11),11);                     // 071 a 081 -> Identificacao do titulo no banco Nosso Numero
        $transacao .= $this->picture_x(substr(trim($dados["nosso_numero"]), -1),1);                   // 082 a 082 -> Digito de auto conferencia do nosso numero
        $transacao .= $this->complementoRegistro(10,'zeros');                     // 083 a 092 -> Desconto de bonificacao por dia (10 zeros)
        $transacao .= $this->picture_9('2',1);                                    // 093 a 093 -> Condicao para emissao da papeleta 1=banco emite / 2=cliente emite
        $transacao .= 'N';                                                 // 094 a 094 -> Se o banco vai por por o boleto em debito em conta N=nao
        $transacao .= $this->complementoRegistro(10,'brancos');                   // 095 a 104 -> deixar 10 brancos
        $transacao .= $this->complementoRegistro(1,'brancos');                    // 105 a 105 -> deixar 1 branco
        $transacao .= $this->picture_9(trim($dados["enderecamento_aviso_debito"]),1);        // 106 a 106 -> enderecamento aviso debito 1=emite / 2=nao emite
        $transacao .= $this->complementoRegistro(2,'brancos');                    // 107 a 108 -> deixar 2 brancos
        $transacao .= "01";                                                // 109 a 110 -> cod. movimento (pag.20) -> 01 = remessa, pedido de registro
        $transacao .= $this->picture_9(substr(trim($dados["numero_documento"]), 0, 9),10);                 // 111 a 120 -> Numero do documento (nosso numero)
        $transacao .= $this->picture_9(trim($dados["data_vencimento_boleto"]),6);            // 121 a 126 -> Data de vencimento
        $transacao .= $this->picture_9(trim($dados["valor_boleto"]),13);                     // 127 a 139 -> valor do titulo
        $transacao .= $this->complementoRegistro(3,"zeros");                      // 140 a 142 -> Ag�ncia/banco encarregado da Cobran�a (Preencher com zeros)
        $transacao .= $this->complementoRegistro(5,"zeros");                      // 143 a 147 -> Agencia depositaria -> preencher com zeros
        $transacao .= $this->picture_x(trim($dados["especie_titulo"]),2);                     // 148 a 159 -> Especie de titulo. 99 = outros / 12=Duplicata servico (DS)
        $transacao .= "N";                                                 // 150 a 150 -> Identificacao -> Sempre 'N'
        $transacao .= $this->picture_9(trim($dados["data_emissao_boleto"]),6);               // 151 a 156 -> Data de emissao do titulo
        $transacao .= $this->complementoRegistro(2,"zeros");                      // 157 a 158 -> 1a. instrucao
        $transacao .= $this->complementoRegistro(2,"zeros");                      // 159 a 160 -> 2a. instrucao
        $transacao .= $this->picture_9(trim($dados["valor_por_dia_de_atraso"]),13);          // 161 a 173 -> Valor cobrado por dia de atraso em percentual = 003 => 0,03
        $transacao .= $this->picture_9(trim($dados["data_limite_desc"]),6);                  // 174 a 179 -> Data limite para desconto
        $transacao .= $this->picture_9(trim($dados["valor_desconto"]),13);                   // 180 a 192 -> Valor do desconto
        $transacao .= $this->picture_9(trim($dados["valor_iof"]),13);                        // 193 a 205 -> Valor do IOF - zeros
        $transacao .= $this->picture_9(trim($dados["valor_abatimento"]),13);                 // 206 a 218 -> Valor do abatimento a ser concedido ou cancelado
        $transacao .= $this->picture_9(trim($dados["tipo_inscricao_pagador"]),2);            // 219 a 220 -> Tipo de pagador/1=CPF/2=CNPJ/3=PIS-PASEP/98=NAO TEM/99=OUTROS
        $transacao .= $this->picture_9(trim($dados["numero_inscricao_pagador"]),14);         // 221 a 234 -> Inscricao do pagador - CPF
        $transacao .= $this->picture_x(trim($dados["nome_pagador"]),40);                     // 235 a 274 -> Nome do pagador
        $transacao .= $this->picture_x(trim($dados["endereco_pagador"]),40);                 // 275 a 314 -> Endereco completo
        $transacao .= $this->complementoRegistro(12,"brancos");                   // 315 a 326 -> 1a. mensagem
        $transacao .= $this->picture_9(trim($dados["cep_pagador"]),5);                       // 327 a 331 -> CEP
        $transacao .= $this->picture_9(trim($dados["cep_pagador_sufixo"]),3);                // 332 a 334 -> Cep (sufixo)
        $transacao .= $this->complementoRegistro(60,"brancos");                   // 335 a 394 -> Sacador/Avalista ou 2a. mensagem
        $transacao .= $this->picture_9($numeroLinha,6).chr(13).chr(10);                         // 395 a 400 -> Numero sequencia do registro

        return $transacao;
    }

    public function trailler($numeroLinha)
    {
        return "9".$this->complementoRegistro(393,"brancos").$this->picture_9($numeroLinha,6).chr(13).chr(10);
    }

    public function retorno($file)
    {
        $fp = fopen($file, "rb");

        $retorno = array();
        $i = 0;
        while (!feof ($fp))
        {
            $linha = fgets($fp, 9999);

            //Lay-out do Arquivo-Retorno - Registro Header Label
            if (substr($linha, 2, 7) == 'RETORNO')
            {
                $retorno["header"]["registro"]  =   trim(substr($linha, 0, 1));
                $retorno["header"]["identificacao"]  =   trim(substr($linha, 1, 1));
                $retorno["header"]["literal"]  =   trim(substr($linha, 2, 7));
                $retorno["header"]["codigo_servico"]  =   trim(substr($linha, 9, 2));
                $retorno["header"]["servico"]  =   trim(substr($linha, 11, 15));
                $retorno["header"]["codigo_empresa"]    =   trim(substr($linha, 26, 20));
                $retorno["header"]["nome_empresa"]      =   trim(substr($linha, 46, 30));
                $retorno["header"]["codigo_banco"]      =   trim(substr($linha, 76, 3));
                $retorno["header"]["nome_banco"]      	=   trim(substr($linha, 79, 15));
                $retorno["header"]["data_gravacao_arquivo"]      	=   trim(substr($linha, 94, 2))."/".trim(substr($linha, 96, 2))."/".trim(substr($linha, 98, 2));
                $retorno["header"]["densidade_gravacao"]      	=   trim(substr($linha, 100, 8));
                $retorno["header"]["aviso_bancario"]    =   trim(substr($linha, 108, 5));
                $retorno["header"]["branco_1"]    =   trim(substr($linha, 113, 266));
                $retorno["header"]["data_credito"]    	=   trim(substr($linha, 379, 2))."/".trim(substr($linha, 381, 2))."/".trim(substr($linha, 383, 2));
                $retorno["header"]["branco_2"]    =   trim(substr($linha, 385, 9));
                $retorno["header"]["sequencial"]  =   trim(substr($linha, 394, 6));
            }


            if (trim(substr($linha, 0, 1)) == 9){
                //implementar
            }

            //Lay-out do Arquivo-Retorno - Registro de Transação – Tipo 1
            if (trim(substr($linha, 0, 1)) == 1)
            {
                $retorno["transacao"][$i]["registro"] = trim(substr($linha, 0, 1));
                $retorno["transacao"][$i]["tipo_inscricao_empresa"] = trim(substr($linha, 1, 2));
                $retorno["transacao"][$i]["inscricao_empresa"] = trim(substr($linha, 3, 14));
                $retorno["transacao"][$i]["zeros"] = trim(substr($linha, 17, 3));
                $retorno["transacao"][$i]["identificacao_empresa_cedente_banco"] = trim(substr($linha, 20, 17));

                $retorno["transacao"][$i]["controle_participante"] = trim(substr($linha, 37, 25));
                $retorno["transacao"][$i]["zeros_1"] = trim(substr($linha, 62, 8));
                $retorno["transacao"][$i]["identificacao_titulo_1"] = trim(substr($linha, 70, 12));
                $retorno["transacao"][$i]["uso_banco_1"] = trim(substr($linha, 82, 10));
                $retorno["transacao"][$i]["uso_banco_2"] = trim(substr($linha, 92, 12));
                $retorno["transacao"][$i]["indicador_rateio_credito"] = trim(substr($linha, 104, 1));
                $retorno["transacao"][$i]["zeros_2"] = trim(substr($linha, 105, 2));
                $retorno["transacao"][$i]["carteira"] = trim(substr($linha, 107, 1));
                $retorno["transacao"][$i]["identificação_ocorrência"] = trim(substr($linha, 108, 2));
                $retorno["transacao"][$i]["data_ocorrencia"] = trim(substr($linha, 110, 2)) . "/" . trim(substr($linha, 112, 2)) . "/" . trim(substr($linha, 114, 2));
                $retorno["transacao"][$i]["numero_documento"] = trim(substr($linha, 116, 10));
                $retorno["transacao"][$i]["identificacao_titulo_2"] = trim(substr($linha, 126, 20));
                $retorno["transacao"][$i]["data_vencimento"] = trim(substr($linha, 146, 2)) . "/" . trim(substr($linha, 148, 2)) . "/" . trim(substr($linha, 150, 2));

                $valor_titulo = (float)trim(substr($linha, 152, 13));
                $valor_pago = (float)trim(substr($linha, 253, 13));

                $retorno["transacao"][$i]["valor_titulo"] = substr($valor_titulo, 0, strlen($valor_titulo) - 2) . "." . substr($valor_titulo, -2);
                $retorno["transacao"][$i]["banco_cobrador"] = trim(substr($linha, 165, 3));

                $retorno["transacao"][$i]["banco_cobrador"] = trim(substr($linha, 165, 3));
                $retorno["transacao"][$i]["agencia_cobradora"] = trim(substr($linha, 168, 5));
                $retorno["transacao"][$i]["especie_titulo"] = trim(substr($linha, 173, 2));

                $retorno["transacao"][$i]["valor_despesa"] = (float)trim(substr($linha, 175, 13));
                $retorno["transacao"][$i]["outras_despesas"] = (float)trim(substr($linha, 188, 13));
                $retorno["transacao"][$i]["juros_operacao_atraso"] = (float)trim(substr($linha, 201, 13));
                $retorno["transacao"][$i]["IOF_devido"] = (float)trim(substr($linha, 214, 13));

                $retorno["transacao"][$i]["valor_abatimento"] = (float)trim(substr($linha, 227, 13));
                $retorno["transacao"][$i]["desconto_concedido"] = (float)trim(substr($linha, 240, 13));
                $retorno["transacao"][$i]["valor_pago"] = substr($valor_pago, 0, strlen($valor_pago) - 2) . "." . substr($valor_pago, -2);
                $retorno["transacao"][$i]["juros_mora"] = (float)trim(substr($linha, 266, 13));
                $retorno["transacao"][$i]["outros_creditos"] = (float)trim(substr($linha, 279, 13));
                $retorno["transacao"][$i]["brancos_1"] = trim(substr($linha, 292, 2));
                $retorno["transacao"][$i]["motivo"] = trim(substr($linha, 294, 1));

                $retorno["transacao"][$i]["data_credito"] = trim(substr($linha, 295, 2)) . "/" . trim(substr($linha, 297, 2)) . "/" . trim(substr($linha, 299, 2));
                $retorno["transacao"][$i]["origem_pagamento"] = trim(substr($linha, 301, 3));
                $retorno["transacao"][$i]["brancos_2"] = trim(substr($linha, 304, 10));

                $retorno["transacao"][$i]["cheque_bradesco"] = trim(substr($linha, 314, 4));
                $retorno["transacao"][$i]["motivos_rejeicoes"] = trim(substr($linha, 318, 10));
                $retorno["transacao"][$i]["brancos_3"] = trim(substr($linha, 328, 40));

                $retorno["transacao"][$i]["numero_cartorio"] = trim(substr($linha, 368, 2));
                $retorno["transacao"][$i]["numero_protocolo"] = trim(substr($linha, 370, 10));
                $retorno["transacao"][$i]["brancos_3"] = trim(substr($linha, 380, 14));
                $retorno["transacao"][$i]["sequencial"] = trim(substr($linha, 394, 6));

                $i++;
            }
        }

        return $retorno;
    }
}