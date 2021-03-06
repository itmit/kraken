@extends('layouts.mainApp')

@section('content')

<div class="col-sm-12 tabs-content">
    <div class="row justify-content-center cont-m">
        <div class="col-md-12">
            <h2>{{ $title }}</h2>
            <table class="table policy-table">
                <thead>
                <tr>
                    <th scope="col">ФИО</th>
                    <th scope="col">Эл. почта</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Организация</th>
                    <th scope="col">Адрес</th>
                </tr>
                </thead>
                <tbody>
                @foreach($clients as $item)
                <?
                    $clientInfo = $item->getClientInfo();
                ?>
                    <tr>
                        <td>{{ $clientInfo->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $clientInfo->phone }}</td>
                        <td>{{ $clientInfo->organization }}</td>
                        <td>{{ $clientInfo->address }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 

<script>

// $(document).on('click', '.delete-news', function() {
//     let isDelete = confirm("Удалить новость? Данное действие невозможно отменить!");

//     if(isDelete)
//     {
//         let id = $(this).data('id');
//         $.ajax({
//             headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
//             dataType: "json",
//             data    : { id: id },
//             url     : 'news/delete',
//             method    : 'delete',
//             success: function (response) {
//                 $(this).closest('.row').remove();
//                 console.log('Удалено!');
//             },
//             error: function (xhr, err) { 
//                 console.log("Error: " + xhr + " " + err);
//             }
//         });
//     }
// });

</script>
@endsection