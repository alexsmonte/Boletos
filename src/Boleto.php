<?php
namespace Asmpkg\Boleto;
use ReflectionClass;

class Boleto
{

    const VERSION = '0.1';

    private $banco;
    public $codigoBanco;
    public $vencimento;
    public $valor;
    public $codigoBarras;
    public $linhaDigitavel;
    public $agencia;
    public $conta;
    public $nossoNumero;

    public function __construct($banco)
    {
        $namespace = new ReflectionClass("Asmpkg\\Boleto\\Banco\\" . $banco);
        $this->banco = $namespace->newInstance();
    }

    public function codigoBanco()
    {
        $this->codigoBanco =   $this->banco->codigoBanco();
        return $this;
    }

    public function conta($conta)
    {
        $this->conta    =   $conta;

        $this->banco->conta($conta);
        return $this;
    }

    public function agencia($agencia)
    {
        $this->agencia  =   $agencia;
        $this->banco->agencia($agencia);
        return $this;
    }

    public function digitoAgencia($digitoAgencia)
    {
        $this->banco->digitoAgencia($digitoAgencia);
        return $this;
    }

    public function digitoConta($digitoConta)
    {
        $this->banco->digitoConta($digitoConta);
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
        $this->nossoNumero  =  $this->banco->nossoNumero($nossoNumero);
        return $this;
    }

    public function valor($valor)
    {
        $this->valor = $valor;
        $this->banco->valor($valor);
        return $this;
    }

    public function vencimento($vencimento)
    {
        $this->vencimento = $vencimento;
        $this->banco->vencimento($vencimento);
        return $this;
    }

    public function linhaDigitavel()
    {
        $this->linhaDigitavel   = $this->banco->linhaDigitavel();
        return $this;
    }

    public function codigoBarras()
    {
        $this->codigoBarras =   $this->banco->codigoBarras();
        return $this;
    }

    private function esquerda($entra, $comp)
    {
        return substr($entra, 0, $comp);
    }

    private function direita($entra, $comp)
    {
        return substr($entra, strlen($entra) - $comp, $comp);
    }

    /**
     * @param $valor
     *
     * codigo retirado do phpboleto
     */
    public function fBarCode($valor)
    {
        $fino = 1;
        $largo = 3;
        $altura = 50;

        $barcodes[0] = "00110";
        $barcodes[1] = "10001";
        $barcodes[2] = "01001";
        $barcodes[3] = "11000";
        $barcodes[4] = "00101";
        $barcodes[5] = "10100";
        $barcodes[6] = "01100";
        $barcodes[7] = "00011";
        $barcodes[8] = "10010";
        $barcodes[9] = "01010";

        for ($f1 = 9; $f1 >= 0; $f1--) {
            for ($f2 = 9; $f2 >= 0; $f2--) {
                $f = ($f1 * 10) + $f2;
                $texto = "";
                for ($i = 1; $i < 6; $i++) {
                    $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
                }
                $barcodes[$f] = $texto;
            }
        }

        $codigoBarra[0]["imagem"] = 'p';
        $codigoBarra[0]["width"] = $fino;
        $codigoBarra[0]["height"] = $altura;

        $codigoBarra[1]["imagem"] = 'b';
        $codigoBarra[1]["width"] = $fino;
        $codigoBarra[1]["height"] = $altura;

        $codigoBarra[2]["imagem"] = 'p';
        $codigoBarra[2]["width"] = $fino;
        $codigoBarra[2]["height"] = $altura;

        $codigoBarra[3]["imagem"] = 'b';
        $codigoBarra[3]["width"] = $fino;
        $codigoBarra[3]["height"] = $altura;

        $texto = $valor;
        if ((strlen($texto) % 2) <> 0)
            $texto = "0" . $texto;

        $contador = 4;
        while (strlen($texto) > 0)
        {
            $i = round($this->esquerda($texto,2));
            $texto = $this->direita($texto,strlen($texto)-2);
            $f = $barcodes[$i];

            for($i=1;$i<11;$i+=2)
            {
                if (substr($f,($i-1),1) == "0")
                    $f1 = $fino ;
                else
                    $f1 = $largo ;

                $codigoBarra[$contador]["imagem"]   = 'p';
                $codigoBarra[$contador]["width"]    = $f1;
                $codigoBarra[$contador]["height"]   = $altura;

                $contador++;

                if (substr($f,$i,1) == "0")
                    $f2 = $fino ;
                else
                    $f2 = $largo ;

                $codigoBarra[$contador]["imagem"]   = 'b';
                $codigoBarra[$contador]["width"]    = $f2;
                $codigoBarra[$contador]["height"]   = $altura;

                $contador++;
            }
        }

        $codigoBarra[$contador]["imagem"]   = 'p';
        $codigoBarra[$contador]["width"]    = $largo;
        $codigoBarra[$contador]["height"]   = $altura;

        $contador++;

        $codigoBarra[$contador]["imagem"]   = 'b';
        $codigoBarra[$contador]["width"]    = $fino;
        $codigoBarra[$contador]["height"]   = $altura;

        $contador++;

        $codigoBarra[$contador]["imagem"]   = 'p';
        $codigoBarra[$contador]["width"]    = 1;
        $codigoBarra[$contador]["height"]   = $altura;

        return $codigoBarra;
    }


}
