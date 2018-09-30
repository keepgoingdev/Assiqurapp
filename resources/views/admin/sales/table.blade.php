<table class="table table-responsive" id="sales-table">
    <thead>
        <tr>
        <th>Seller ID</th>
        <th>Age</th>
        <!--<th>Packagetype</th>-->
        <th>Price</th>
        <th>Contractortype</th>
        <th>Contractorfirstname</th>
        <th>Contractorlastname</th>
        <th>Contractoraddress</th>
        <th>Contractortaxcode</th>
        <th>Contractorbirthday</th>
        <!--<th>Contractorbirthplace</th>-->
        <th>Contractoremail</th>
        <th>Contractortelephone</th>
        <!--
        <th>Insuredtype</th>
        <th>Insuredfirstname</th>
        <th>Insuredlastname</th>
        <th>Insuredaddress</th>
        <th>Insuredtaxcode</th>
        <th>Insuredbirthday</th>
        <th>Insuredbirthplace</th>
        <th>Insuredemail</th>
        <th>Insuredtelephone</th>
        <th>Deathbentype</th>
        <th>Deathbenfirstname</th>
        <th>Deathbenlastname</th>
        <th>Deathbenaddress</th>
        <th>Deathbentaxcode</th>
        <th>Deathbenbirthday</th>
        <th>Deathbenbirthplace</th>
        <th>Deathbenemail</th>
        <th>Deathbentelephone</th>
        <th>Receiveemail</th>-->
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($sales as $sale)
        <tr>
            <td>{!! $sale->seller_id !!}</td>
            <td>{!! $sale->age !!}</td>
            <!--<td>{!! $sale->packageType !!}</td>-->
            <td>{!! $sale->price !!}</td>
            <td>{!! $sale->contractorType !!}</td>
            <td>{!! $sale->contractorFirstName !!}</td>
            <td>{!! $sale->contractorLastName !!}</td>
            <td>{!! $sale->contractorAddress !!}</td>
            <td>{!! $sale->contractorTaxCode !!}</td>
            <td>{!! $sale->contractorBirthday !!}</td>
            <!--<td>{!! $sale->contractorBirthPlace !!}</td>-->
            <td>{!! $sale->contractorEmail !!}</td>
            <td>{!! $sale->contractorTelephone !!}</td>
            <!--
            <td>{!! $sale->insuredType !!}</td>
            <td>{!! $sale->insuredFirstName !!}</td>
            <td>{!! $sale->insuredLastName !!}</td>
            <td>{!! $sale->insuredAddress !!}</td>
            <td>{!! $sale->insuredTaxCode !!}</td>
            <td>{!! $sale->insuredBirthday !!}</td>
            <td>{!! $sale->insuredBirthPlace !!}</td>
            <td>{!! $sale->insuredEmail !!}</td>
            <td>{!! $sale->insuredTelephone !!}</td>
            <td>{!! $sale->deathBenType !!}</td>
            <td>{!! $sale->deathBenFirstName !!}</td>
            <td>{!! $sale->deathBenLastName !!}</td>
            <td>{!! $sale->deathBenAddress !!}</td>
            <td>{!! $sale->deathBenTaxCode !!}</td>
            <td>{!! $sale->deathBenBirthday !!}</td>
            <td>{!! $sale->deathBenBirthPlace !!}</td>
            <td>{!! $sale->deathBenEmail !!}</td>
            <td>{!! $sale->deathBenTelephone !!}</td>
            <td>{!! $sale->receiveEmail !!}</td>
            -->
            <td>
                {!! Form::open(['route' => ['admin.sales.destroy', $sale->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.sales.show', [$sale->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.sales.edit', [$sale->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Sei sicuro?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
