@extends('layouts.mainApp')

@section('content')

<div class="col-sm-12 tabs-content">
    <div class="row justify-content-center cont-m">
        <div class="col-md-12">
            <h2>{{ $title }}</h2>
            <table class="table policy-table">
                <thead>
                <tr>
                    <th scope="col">Заказчик</th>
                    <th scope="col">Мастер</th>
                    <th scope="col">Род работ</th>
                    <th scope="col">Срочность</th>
                    <th scope="col">Описание</th>
                    <th scope="col">Адрес</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Создана</th>
                </tr>
                </thead>
                <tbody>
                @foreach($inquiries as $item)
                <?
                $detail = $item->getInquiryDetail();
                $client = $item->getClient();
                $master = $item->getMaster();
                ?>
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>
                            @if ($master)
                                {{ $master->name }}
                            @else
                                Мастер еще не назначен
                            @endif
                        </td>
                        <td>{{ $detail->getWork()->work }}</td>
                        <td>{{ $detail->urgency }}</td>
                        <td>{{ $detail->description }}</td>
                        <td>{{ $detail->address }}</td>
                        <td>{{ $detail->status }}</td>
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