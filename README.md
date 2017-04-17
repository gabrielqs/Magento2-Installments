# Magento2-Installments
Exibição de parcelamento genérico a ser utilizado por módulos de métodos de pagamento.
Este módulo é responsável pela apresentação do parcelamento para produtos e carrinhos de compra. Os módulos de métodos de pagamento que optarem por exibir suas parcelas utilizando este módulo serão apenas responsáveis pelo correto cálculo.
O parcelamento será exibido nas páginas de Categorias, Detalhes do Produto e Carrinho de compras.
 
Generic installments presentation module, to be used by payment method modules.
This module is responsible for the visual presentation of payment installments for the products and shopping cart. Payment methods are then only responsible by the correct calculations and charging using such installments
Installments will be shown at the category pages, product detail pages and shopping cart.


## Installation
Use composer: `composer require gabrielqs/installments`

## Configuration
All configurations are available at Store -> Configuration -> Sales -> Installments

## Adding instalments for your payment methods
In your etc/config.xml add a new node, similar to the one below:
`
<config>
    <default>
        <installments>
            <payment_methods>
                <cielo_webservice>
                    <installments_helper>Gabrielqs\Cielo\Helper\Webservice\Installments</installments_helper>
                </cielo_webservice>
            </payment_methods>
        </installments>
    </default>
</config>
`

Then create the referred class. See the module Gabrielqs\Cielo for an example.