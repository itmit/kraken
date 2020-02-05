@extends('layouts.mainApp')

@section('content')

<div class="col-sm-12 tabs-content">
    <div class="row justify-content-center cont-m">
        <div class="col-md-12">
            <a href="{{ route('auth.masters.create') }}" class="btn btn-light">Создать мастера</a>
            <h2>{{ $title }}</h2>
            <table class="table policy-table">
                <thead>
                <tr>
                    <th scope="col">Имя</th>
                    <th scope="col">Квалификация</th>
                    <th scope="col">Специальности</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Почта</th>
                    <th scope="col">Рейтинг</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Зарегистрирован</th>
                </tr>
                </thead>
                <tbody>
                @foreach($masters as $item)
                <?
                    $masterInfo = $item->getMasterInfo();
                ?>
                    <tr>
                        <td>{{ $masterInfo->name }}</td>
                        <td>{{ $masterInfo->qualification }}</td>
                        <td>{{ $masterInfo->work }}</td>
                        <td>{{ $masterInfo->phone }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $masterInfo->rating }}</td>
                        <td>{{ $masterInfo->status }}</td>
                        <td>{{ date('H:i d.m.Y', strtotime($item->created_at->timezone('Europe/Moscow'))) }}</td>
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