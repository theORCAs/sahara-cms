<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th scope="col"> Training Category </th>
            <th scope="col"> Training Name</th>
            <th scope="col"> Rate </th>
        </tr>
        </thead>
        <tbody>
        @foreach($liste as $row)
            <tr>
                <td>{{$row->kategori_adi}}</td>
                <td>{{$row->kodu." ".$row->adi}}</td>
                <td>{{$row->rate}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
