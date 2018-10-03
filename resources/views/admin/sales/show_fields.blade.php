<div class = "container-fluid">
    <div class = "row">
        <div class = "col-md-3">
            <div class="form-group">
                    {!! Form::label('id', 'ID:') !!}
                    <p>{!! $sale->id !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <div class="form-group">
                {!! Form::label('seller_id', 'Vendite ID:') !!}
                <p>{!! $sale->seller_id !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <div class="form-group">
                {!! Form::label('age', 'Età:') !!}
                <p>{!! $sale->age !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <div class="form-group">
                {!! Form::label('price', 'Prezzo:') !!}
                <p>{!! $sale->price !!}</p>
            </div>
        </div>
    </div>
    <div class = "row" style = "margin-top:20px">
        <div class = "col-md-3">
                <!-- Contractortype Field -->
                <div class="form-group">
                    {!! Form::label('contractorType', 'Contraente Genere:') !!}
                    <p>{!! $sale->contractorType !!}</p>
                </div>
        </div>
    </div>
    <div class = "row">
        <div class = "col-md-3">
            <!-- Contractorfirstname Field -->
            <div class="form-group">
                {!! Form::label('contractorFirstName', 'Contraente Nome:') !!}
                <p>{!! $sale->contractorFirstName !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Contractorlastname Field -->
            <div class="form-group">
                {!! Form::label('contractorLastName', 'Contraente Cognome:') !!}
                <p>{!! $sale->contractorLastName !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Contractoraddress Field -->
            <div class="form-group">
                {!! Form::label('contractorAddress', 'Contraente Indirizzo:') !!}
                <p>{!! $sale->contractorAddress !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Contractortaxcode Field -->
            <div class="form-group">
                {!! Form::label('contractorTaxCode', 'Contraente Luogo:') !!}
                <p>{!! $sale->contractorTaxCode !!}</p>
            </div>
        </div>
    </div>
    <div class = "row">
        <div class = "col-md-3">
            <!-- Contractorbirthday Field -->
            <div class="form-group">
                {!! Form::label('contractorBirthday', 'Contraente Compleanno:') !!}
                <p>{!! $sale->contractorBirthday !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Contractorbirthplace Field -->
            <div class="form-group">
                {!! Form::label('contractorBirthPlace', 'Contraente luogo di nascita:') !!}
                <p>{!! $sale->contractorBirthPlace !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Contractoremail Field -->
            <div class="form-group">
                {!! Form::label('contractorEmail', 'Contraente EMail:') !!}
                <p>{!! $sale->contractorEmail !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Contractortelephone Field -->
            <div class="form-group">
                {!! Form::label('contractorTelephone', 'Contraente Telefono:') !!}
                <p>{!! $sale->contractorTelephone !!}</p>
            </div>
        </div>        
    </div>
    <div class = "row" style = "margin-top:50px">
        <div class = "col-md-3">
            <!-- Insuredtype Field -->
            <div class="form-group">
                {!! Form::label('insuredType', 'Assicurato Genere:') !!}
                <p>{!! $sale->insuredType !!}</p>
            </div>
        </div>
    </div>
    <div class = "row">
        <div class = "col-md-3">
                <!-- Insuredfirstname Field -->
                <div class="form-group">
                    {!! Form::label('insuredFirstName', 'Assicurato Nome:') !!}
                    <p>{!! $sale->insuredFirstName !!}</p>
                </div>
        </div>            
        <div class = "col-md-3">
                <!-- Insuredlastname Field -->
                <div class="form-group">
                    {!! Form::label('insuredLastName', 'Assicurato Cognome:') !!}
                    <p>{!! $sale->insuredLastName !!}</p>
                </div>
        </div>
        <div class = "col-md-3">
                <!-- Insuredaddress Field -->
                <div class="form-group">
                    {!! Form::label('insuredAddress', 'Assicurato Indirizzo:') !!}
                    <p>{!! $sale->insuredAddress !!}</p>
                </div>
        </div>
        <div class = "col-md-3">
                <!-- Insuredtaxcode Field -->
                <div class="form-group">
                    {!! Form::label('insuredTaxCode', 'Assicurato Luogo:') !!}
                    <p>{!! $sale->insuredTaxCode !!}</p>
                </div>
        </div>
    </div>
    <div class = "row">
            <div class = "col-md-3">
                <!-- Insuredbirthday Field -->
                <div class="form-group">
                    {!! Form::label('insuredBirthday', 'Assicurato Compleanno:') !!}
                    <p>{!! $sale->insuredBirthday !!}</p>
                </div>
            </div>            
            <div class = "col-md-3">
                <!-- Insuredbirthplace Field -->
                <div class="form-group">
                    {!! Form::label('insuredBirthPlace', 'Assicurato luogo di nascita:') !!}
                    <p>{!! $sale->insuredBirthPlace !!}</p>
                </div>
            </div>
            <div class = "col-md-3">
                <!-- Insuredemail Field -->
                <div class="form-group">
                    {!! Form::label('insuredEmail', 'Assicurato EMail:') !!}
                    <p>{!! $sale->insuredEmail !!}</p>
                </div>
            </div>
            <div class = "col-md-3">
                <!-- Insuredtelephone Field -->
                <div class="form-group">
                    {!! Form::label('insuredTelephone', 'Assicurato Telefono:') !!}
                    <p>{!! $sale->insuredTelephone !!}</p>
                </div>
            </div>
    </div>
    <div class = "row" style = "margin-top:50px">
        <div class = "col-md-3">
            <!-- Deathbentype Field -->
            <div class="form-group">
                {!! Form::label('deathBenType', 'Beneficiario Caso Morte Genere:') !!}
                <p>{!! $sale->deathBenType !!}</p>
            </div>
        </div>            
    </div>
    <div class = "row">
        <div class = "col-md-3">
                <!-- Deathbenfirstname Field -->
                <div class="form-group">
                    {!! Form::label('deathBenFirstName', 'Beneficiario Caso Morte Nome:') !!}
                    <p>{!! $sale->deathBenFirstName !!}</p>
                </div>
        </div>            
        <div class = "col-md-3">
                <!-- Deathbenlastname Field -->
                <div class="form-group">
                    {!! Form::label('deathBenLastName', 'Beneficiario Caso Morte Cognome:') !!}
                    <p>{!! $sale->deathBenLastName !!}</p>
                </div>
        </div>
        <div class = "col-md-3">
                <!-- Deathbenaddress Field -->
                <div class="form-group">
                    {!! Form::label('deathBenAddress', 'Beneficiario Caso Morte Indirizzo:') !!}
                    <p>{!! $sale->deathBenAddress !!}</p>
                </div>
        </div>
        <div class = "col-md-3">
                <!-- Deathbentaxcode Field -->
                <div class="form-group">
                    {!! Form::label('deathBenTaxCode', 'Beneficiario Caso Morte Luogo:') !!}
                    <p>{!! $sale->deathBenTaxCode !!}</p>
                </div>
        </div>
    </div>
    <div class = "row">
        <div class = "col-md-3">
                <!-- Deathbenbirthday Field -->
                <div class="form-group">
                    {!! Form::label('deathBenBirthday', 'Beneficiario Caso Morte Compleanno:') !!}
                    <p>{!! $sale->deathBenBirthday !!}</p>
                </div>
        </div>            
        <div class = "col-md-3">
                <!-- Deathbenbirthplace Field -->
                <div class="form-group">
                    {!! Form::label('deathBenBirthPlace', 'Beneficiario luogo di nascita:') !!}
                    <p>{!! $sale->deathBenBirthPlace !!}</p>
                </div>
        </div>
        <div class = "col-md-3">
                <!-- Deathbenemail Field -->
                <div class="form-group">
                    {!! Form::label('deathBenEmail', 'Beneficiario Caso Morte EMail:') !!}
                    <p>{!! $sale->deathBenEmail !!}</p>
                </div>
        </div>
        <div class = "col-md-3">
                <!-- Deathbentelephone Field -->
                <div class="form-group">
                    {!! Form::label('deathBenTelephone', 'Beneficiario Caso Morte Telefono:') !!}
                    <p>{!! $sale->deathBenTelephone !!}</p>
                </div>
        </div>
    </div>

    <div class = "row" style = "margin-top:40px">
        <div class = "col-md-3">
            <!-- Receiveemail Field -->
            <div class="form-group">
                {!! Form::label('receiveEmail', 'Ricevte EMail:') !!}
                <p>{!! $sale->receiveEmail !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Created At Field -->
            <div class="form-group">
                {!! Form::label('created_at', 'Created At:') !!}
                <p>{!! $sale->created_at !!}</p>
            </div>
        </div>
        <div class = "col-md-3">
            <!-- Updated At Field -->
            <div class="form-group">
                {!! Form::label('updated_at', 'Updated At:') !!}
                <p>{!! $sale->updated_at !!}</p>
            </div>
        </div>
    </div>
        
</div>