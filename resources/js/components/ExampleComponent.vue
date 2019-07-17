<template>
    <div class="container">
        <div class="card">


            <div class="list-group" data-spy="scroll">
                <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-primary">This is a primary
                    list group item</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-secondary">This is a secondary
                    list group item</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-success">This is a success
                    list group item</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-danger">This is a danger list
                    group item</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-warning">This is a warning
                    list group item</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-info">This is a info list
                    group item</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-light">This is a light list
                    group item</a>
                <a href="#" class="list-group-item list-group-item-action list-group-item-dark">This is a dark list
                    group item</a>
            </div>
        </div>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Введите текст" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">Отправить</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
