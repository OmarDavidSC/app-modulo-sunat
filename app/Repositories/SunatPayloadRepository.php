<?php

namespace App\Repositories;

class SunatPayloadRepository
{
    public function build($data)
    {
        $tipoDocumento = $data['comprobante']['tipo_documento'];

        switch ($tipoDocumento) {
            case '01':
                return $this->buildFactura($data);
            case '03':
                return $this->buildBoleta($data);
            case '07':
                return $this->buildNotaCredito($data);
            case '08':
                return $this->buildNotaDebito($data);
            default:
                throw new \Exception('Tipo de documento no soportado.');
        }
    }

    public function buildFactura($data)
    {
        return $this->buildInvoice($data, '01');
    }

    public function buildBoleta($data)
    {
        return $this->buildInvoice($data, '03');
    }

    public function buildNotaCredito($data)
    {
        return $this->buildInvoice($data, '07');
    }

    public function buildNotaDebito($data)
    {
        return $this->buildInvoice($data, '08');
    }

    private function buildInvoice($data, $tipoDocumento)
    {
        $empresa = $data['empresa'];
        $cliente = $data['cliente'];
        $comp = $data['comprobante'];
        $moneda = $comp['moneda'] ?? 'PEN';
        $tipoPago = $comp['tipo_pago'] ?? 'Contado';

        $correlativo = $comp['correlativo'];
        $fileName = $empresa['ruc'] . '-' . $tipoDocumento . '-' . $comp['serie'] . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT);
        $invoiceLines = [];

        $subtotal = 0;
        $igvTotal = 0;
        $total = 0;

        foreach ($data['items'] as $index => $item) {
            $cantidad = (float)$item['cantidad'];
            $precio = (float)$item['precio'];
            $valorVenta = round($precio / 1.18, 2);
            $subTotalLinea = round($valorVenta * $cantidad, 2);
            $igv = round(($precio * $cantidad) - $subTotalLinea, 2);
            $importe = round($precio * $cantidad, 2);
            $subtotal += $subTotalLinea;
            $igvTotal += $igv;
            $total += $importe;
            $invoiceLines[] = [
                'cbc:ID' => [
                    '_text' => $index + 1
                ],
                'cbc:InvoicedQuantity' => [
                    '_attributes' => ['unitCode' => 'NIU'],
                    '_text' => $cantidad
                ],
                'cbc:LineExtensionAmount' => [
                    '_attributes' => [
                        'currencyID' => $moneda
                    ],
                    '_text' => $subTotalLinea
                ],
                'cac:PricingReference' => [
                    'cac:AlternativeConditionPrice' => [
                        'cbc:PriceAmount' => [
                            '_attributes' => ['currencyID' => $moneda],
                            '_text' => $precio
                        ],
                        'cbc:PriceTypeCode' => ['_text' => '01']
                    ]
                ],
                'cac:TaxTotal' => [
                    'cbc:TaxAmount' => [
                        '_attributes' => ['currencyID' => $moneda],
                        '_text' => $igv
                    ],
                    'cac:TaxSubtotal' => [[
                        'cbc:TaxableAmount' => [
                            '_attributes' => ['currencyID' => $moneda],
                            '_text' => $subTotalLinea
                        ],
                        'cbc:TaxAmount' => [
                            '_attributes' => ['currencyID' => $moneda],
                            '_text' => $igv
                        ],
                        'cac:TaxCategory' => [
                            'cbc:Percent' => ['_text' => 18],
                            'cbc:TaxExemptionReasonCode' => ['_text' => '10'],
                            'cac:TaxScheme' => [
                                'cbc:ID' => ['_text' => '1000'],
                                'cbc:Name' => ['_text' => 'IGV'],
                                'cbc:TaxTypeCode' => ['_text' => 'VAT']
                            ]
                        ]
                    ]]
                ],
                'cac:Item' => [
                    'cbc:Description' => ['_text' => $item['descripcion']]
                ],
                'cac:Price' => [
                    'cbc:PriceAmount' => [
                        '_attributes' => ['currencyID' => $moneda],
                        '_text' => $valorVenta
                    ]
                ]
            ];
        }
        return [
            'personaId' => $empresa['persona_id'],
            'personaToken' => $empresa['persona_token'],
            'fileName' => $fileName,
            'documentBody' => [
                'cbc:UBLVersionID' => ['_text' => '2.1'],
                'cbc:CustomizationID' => ['_text' => '2.0'],
                'cbc:ProfileID' => ['_text' => $tipoDocumento === '01' ? '0101' : '0101'],
                'cbc:ID' => ['_text' => $comp['serie'] . '-' . str_pad($correlativo, 8, '0', STR_PAD_LEFT)],
                'cbc:IssueDate' => ['_text' => date('Y-m-d')],
                'cbc:IssueTime' => ['_text' => date('H:i:s')],
                'cbc:InvoiceTypeCode' => [
                    '_attributes' => ['listID' => '0101'],
                    '_text' => $tipoDocumento
                ],
                'cbc:DocumentCurrencyCode' => ['_text' => $comp['moneda'] ?? 'PEN'],
                'cac:AccountingSupplierParty' => [
                    'cac:Party' => [
                        'cac:PartyIdentification' => [
                            'cbc:ID' => [
                                '_attributes' => ['schemeID' => '6'],
                                '_text' => $empresa['ruc']
                            ]
                        ],
                        'cac:PartyName' => [
                            'cbc:Name' => ['_text' => $empresa['nombre_comercial'] ?? $empresa['razon_social']]
                        ],
                        'cac:PartyLegalEntity' => [
                            'cbc:RegistrationName' => [
                                '_text' => $empresa['razon_social']
                            ],
                            'cac:RegistrationAddress' => [
                                'cbc:AddressTypeCode' => ['_text' => '0000'],
                                'cac:AddressLine' => [
                                    'cbc:Line' => ['_text' => $empresa['direccion'] ?? '-']
                                ]
                            ]
                        ]
                    ]
                ],
                'cac:AccountingCustomerParty' => [
                    'cac:Party' => [
                        'cac:PartyIdentification' => [
                            'cbc:ID' => [
                                '_attributes' => ['schemeID' => $cliente['tipo_documento']],
                                '_text' => $cliente['numero_documento']
                            ]
                        ],
                        'cac:PartyLegalEntity' => [
                            'cbc:RegistrationName' => ['_text' => $cliente['nombre']]
                        ]
                    ]
                ],
                'cbc:Note' => [[
                    '_text' => 'SON ' . number_format($total, 2) . ' SOLES',
                    '_attributes' => ['languageLocaleID' => '1000']
                ]],
                'cac:TaxTotal' => [
                    'cbc:TaxAmount' => [
                        '_attributes' => ['currencyID' => $moneda],
                        '_text' => round($igvTotal, 2)
                    ],
                    'cac:TaxSubtotal' => [[
                        'cbc:TaxableAmount' => [
                            '_attributes' => ['currencyID' => $moneda],
                            '_text' => round($subtotal, 2)
                        ],
                        'cbc:TaxAmount' => [
                            '_attributes' => ['currencyID' => $moneda],
                            '_text' => round($igvTotal, 2)
                        ],
                        'cac:TaxCategory' => [
                            'cac:TaxScheme' => [
                                'cbc:ID' => ['_text' => '1000'],
                                'cbc:Name' => ['_text' => 'IGV'],
                                'cbc:TaxTypeCode' => ['_text' => 'VAT']
                            ]
                        ]
                    ]]
                ],
                'cac:LegalMonetaryTotal' => [
                    'cbc:LineExtensionAmount' => [
                        '_attributes' => ['currencyID' => $moneda],
                        '_text' => round($subtotal, 2)
                    ],
                    'cbc:TaxInclusiveAmount' => [
                        '_attributes' => ['currencyID' => $moneda],
                        '_text' => round($total, 2)
                    ],
                    'cbc:PayableAmount' => [
                        '_attributes' => ['currencyID' => $moneda],
                        '_text' => round($total, 2)
                    ]
                ],
                'cac:PaymentTerms' => [[
                    'cbc:ID' => ['_text' => 'FormaPago'],
                    'cbc:PaymentMeansID' => ['_text' => $tipoPago]
                ]],
                'cac:InvoiceLine' => $invoiceLines
            ]
        ];
    }
}
