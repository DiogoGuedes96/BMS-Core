<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #000000E0;
            opacity: 1;
            font-size: 12px;
        }

        .calls-table {
            width: 100%;
            border-collapse: collapse;
        }

        .calls-table td,
        th {
            padding: 10px;
            border-top: 1px solid #0000000F;
            border-bottom: 1px solid #0000000F;
        }

        .calls-table th:first-child,
        td:first-child {
            border-left: 1px solid #0000000F;
        }

        .calls-table th:last-child,
        td:last-child {
            border-right: 1px solid #0000000F;
        }

        .calls-table th {
            background-color: #FAFAFA;
        }

        .calls-table tfoot td {
            border: none;
        }

        .calls-total {
            text-align: right;
        }

        .calls-table-detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .calls-table-detail td {
            padding: 10px;
        }

        .calls-table-detail td {
            border-top: 1px solid #E8E8E8;
            border-bottom: 1px solid #E8E8E8;
        }

        .calls-table-detail td {
            border-left: none;
            border-right: none;
        }

        .calls-table-detail tr:nth-child(even) {
            background-color: #FAFAFA;
        }

        .calls-table-detail tr:last-child {
            border-bottom: 0px;
        }

        .calls-table-detail td:last-child {
            border-right: 0px;
            border-left: 0px;
            border-bottom: 0px;
        }

        .size-first-column {
            width: 65%;
        }

        .size-second-column {
            width: 35%;
            text-align: end;
        }

        .paddingTop24 {
            padding-top: 24px;
        }

        .padding12 {
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .col-content-center {
            text-align: center;
        }

        .col-widht-90{
            width: 90px;
        }

        .footer-text {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            padding: 10px;
            background-color: #E8E8E8;
            border-top: 1px solid #0000000F;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="calls-body">
        <img src="{{ $imageSrc }}" alt="logo" width="200" height="auto" />
        <h3 class="margin12">Filtro Aplicado: {{ !empty($search) ? $search : '' }}</h3>
        <h3 class="margin12">Chamadas exportadas de: <u>{{ $searchStartDate }} a {{ $searchEndDate }}</u></h3>
        <table class="calls-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Contacto</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>NIF</th>
                    <th>Morada</th>
                    <th>Operador</th>
                    <th>Motivo da Chamada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($calls as $call)
                <tr>
                    <td class="col-widht-90">{{ !empty($call['entity']) ? $call['entity']['name'] : 'Não Registado' }}</td>
                    <td class="col-content-center">{{ !empty($call['callee_phone'])      ? $call['callee_phone']      : $call['caller_phone'] }}</td>
                    <td class="col-content-center">{{ !empty($call['call_created_at'])   ? \Carbon\Carbon::parse($call['call_created_at'])->format('d/m/Y') : '' }}</td>
                    <td class="col-content-center">{{ !empty($call['call_created_at'])   ? \Carbon\Carbon::parse($call['call_created_at'])->format('H:i:s') : '' }}</td>
                    <td class="col-content-center">{{ !empty($call['entity']['nif'])     ? $call['entity']['nif']     : '' }}</td>
                    <td>{{ !empty($call['entity']['address']) ? $call['entity']['address'] : '' }}</td> 
                    <td class="col-content-center">{{ !empty($call['operator_name'])     ? $call['operator_name']     : '-' }}</td>
                    <td class="col-content-center">{{ !empty($call['call_reason'])       ? $call['call_reason']       : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>