<?php

namespace Asmpkg\Boleto;


interface BoletoInterface
{

    public function codigoBanco();
    public function codigoCedente($codigoCedente);
    public function carteira($carteira);
    public function nossoNumero($nossoNumero);
    public function valor($valor);
    public function linhaDigitavel();
    public function codigoBarras();


}