<?php

    require 'Predis/Predis/Autoload.php';

    use Predis\Client;
    $redis = new Client();
    $data = $redis->lrange('names',0,-1);

    // logic for post within same file or with ajax request
    if(isset($_POST['action'])){
        $action = $_POST['action'];
        if($action == ''){
            echo json_encode(array('status' => 'error','msg' => 'empty action!'));
            exit;
        }else if($action == 'lpush'){
            if(isset($_POST['name']) && $_POST['name'] != "" && $_POST['name'] != null){
                // cek for max lrange
                if(count($data) < 2){
                    $redis->lpush('names',$_POST['name']);
                    echo json_encode(array('status' => 'success','msg' => 'Successfully add name '.$_POST['name']));
                    exit;
                }else{
                    echo json_encode(array('status'=> 'error','msg' => 'List has contain 20 names'));
                    exit;
                }
            }else{
                echo json_encode(array('status'=> 'error','msg'=> 'empty name!'));
                exit;
            }
        }else if($action == 'rpush'){
            if(isset($_POST['name']) && $_POST['name'] != "" && $_POST['name'] != null){
                // cek for max lrange
                if(count($data) < 2){
                    $redis->rpush('names',$_POST['name']);
                    echo json_encode(array('status' => 'success','msg' => 'Successfully add name '.$_POST['name']));
                    exit;
                }else{
                    echo json_encode(array('status'=> 'error','msg' => 'List has contain 20 names'));
                    exit;
                }
            }else{
                echo json_encode(array('status'=> 'error','msg'=> 'empty name!'));
                exit;
            }
        }else if($action == 'lpop'){
            // cek for max lrange
            if(count($data) >= 1){
                $res = $redis->lpop('names');
                echo json_encode(array('status' => 'success','msg' => 'Successfully remove name '.$res));
                exit;
            }else{
                echo json_encode(array('status'=> 'error','msg' => 'List already empty!'));
                exit;
            }
        }else if($action == 'rpop'){
            // cek for max lrange
            if(count($data) >= 1){
                $res = $redis->rpop('names');
                echo json_encode(array('status' => 'success','msg' => 'Successfully remove name '.$res));
                exit;
            }else{
                echo json_encode(array('status'=> 'error','msg' => 'List already empty!'));
                exit;
            }
        }else if($action == 'del'){
            // cek for max lrange
            if(count($data) >= 1){
                $res = $redis->del('names');
                echo json_encode(array('status' => 'success','msg' => 'Successfully Deleted all item in the list'));
                exit;
            }else{
                echo json_encode(array('status'=> 'error','msg' => 'List already empty!'));
                exit;
            }
        }else{
            echo json_encode(array('status'=> 'error','msg'=> 'undefined action type!'));
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- CDN for jquery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
        <!-- CDN for Tailwind -->
        <script src="https://cdn.tailwindcss.com/3.3.0"></script>
        <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
        <!-- CDN for Tailwind Element -->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css" />
        <!-- CDN for SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link
            href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap"
            rel="stylesheet" />

        <style>
        @import url('https://fonts.googleapis.com/css2?family=Spinnaker&display=swap');
        *{font-family : 'Spinnaker',sans-serif !important}
        </style>
    </head>
    <body class="overflow-x-hidden overflow-y-auto">
    <div class="w-screen min-h-screen h-full pb-16 bg-slate-300 pt-16 justify-center items-center overflow-x-hidden overflow-y-auto">
        <div class="flex flex-col w-3/4 mx-auto">
            <div class="text-center mb-5">
                <h2 class="text-7xl text-white font-bold">REDIS - PDDS</h2>
                <h4 class="text-4xl text-white">Christopher Julius</h4>
                <h4 class="text-4xl text-white">C14210073</h4>
            </div>  
        </div>
            <div class="w-1/2 shadow-2xl bg-white rounded-xl mx-auto py-8 pb-5">
                <div class="px-8 justify-center items-center">
                <div class="overflow-hidden w-full mx-auto rounded-xl">
                    <table class="text-left text-sm font-light min-w-full">
                        <thead class="border-b bg-slate-500 font-medium">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-white text-center" style="width:20%;">#</th>
                                <th scope="col" class="px-6 py-4 text-white text-center" style="width:80;">Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if($data == null):
                            ?>
                                <tr class="border-b bg-neutral-100">
                                    <td scope="col" colspan="2" class="text-center px-6 py-4 font-bold">Belum ada data</td>
                                </tr>
                            <?php
                                endif;
                                if($data != null):
                                foreach ($data as $key => $value):
                            ?>
                                <tr
                                class="border-b bg-neutral-100">
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-center"><?= $key+1 ?></td>
                                <td class="whitespace-nowrap px-6 py-4 text-center"><?= $value ?></td>
                                </tr>
                            <?php
                                endforeach;
                                endif;
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="grid grid-cols-4 gap-3 mt-8 mb-3">
                    <div class="col-span-2">
                        <div class="relative mb-3 w-full h-full" data-te-input-wrapper-init>
                            <input
                                type="text"
                                id="name"
                                class="peer block min-h-[auto] w-full h-full rounded border-0 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 peer-focus:text-primary data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:peer-focus:text-primary [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0"
                                placeholder="Input Nama" />
                            <label
                                for="name"
                                class="pointer-events-none absolute left-3 top-0 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-primary"
                                >Input Nama
                            </label>
                        </div>
                    </div>
                    <div>
                        <button
                        type="button"
                        data-te-ripple-init
                        data-te-ripple-color="light"
                        id="lpush"
                        class="h-full w-full rounded bg-primary-600 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                        style="background-color: rgb(48,97,175) !important">
                            Push Left
                        </button>
                    </div>
                    <div>
                        <button
                        type="button"
                        data-te-ripple-init
                        data-te-ripple-color="light"
                        id="rpush"
                        class="h-full w-full rounded bg-primary-600 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                        style="background-color: rgb(48,97,175) !important">
                            Push Right
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 mb-8">
                    <div>
                        <button
                        type="button"
                        data-te-ripple-init
                        data-te-ripple-color="light"
                        id="lpop"
                        class="h-full w-full rounded bg-danger-600 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-danger-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                        style="background-color: rgb(212,42,70) !important">
                            Pop Left
                        </button>
                    </div>
                    <div>
                        <button
                        type="button"
                        data-te-ripple-init
                        data-te-ripple-color="light"
                        id="delete"
                        class="h-full w-full rounded bg-danger-600 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                        style="background-color: rgb(212,42,70) !important">
                            Delete all
                        </button>
                    </div>
                    <div>
                        <button
                        type="button"
                        data-te-ripple-init
                        data-te-ripple-color="light"
                        id="rpop"
                        class="h-full w-full rounded bg-danger-600 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-danger-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-danger-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
                        style="background-color: rgb(212,42,70) !important">
                            Pop Right
                        </button>
                    </div>
                </div>

                </div>
            </div>
            </div>
        </div>
    </body>
    <!-- Script for TW Element -->
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <script>
        $(document).ready(function(){
            function ajaxCall(action=null,name=null){
                if(name == null){
                    $.ajax({
                        method : 'POST',
                        data : {
                            'action' : action,
                        },
                        success : function(e){
                            let res = JSON.parse(e);
                            if(res.status == 'success'){
                                Swal.fire({
                                    title : 'Success',
                                    icon : 'success',
                                    text : res.msg
                                }).then((result) => {
                                    window.location.reload();
                                })
                            }else{
                                Swal.fire({
                                    title : 'Error',
                                    icon : 'error',
                                    text : res.msg
                                })
                            }
                        }
                    })
                }else{
                    $.ajax({
                        method : 'POST',
                        data : {
                            'action' : action,
                            'name' : name
                        },
                        success : function(e){
                            let res = JSON.parse(e);
                            if(res.status == 'success'){
                                Swal.fire({
                                    title : 'Success',
                                    icon : 'success',
                                    text : res.msg
                                }).then((result) => {
                                    window.location.reload();
                                })
                            }else{
                                Swal.fire({
                                    title : 'Error',
                                    icon : 'error',
                                    text : res.msg
                                })
                            }
                        }
                    })
                }
            }
            $('#lpush').on('click',function(){
                ajaxCall('lpush',$('#name').val())
            })
            $('#rpush').on('click',function(){
                ajaxCall('rpush',$('#name').val())
            })
            $('#lpop').on('click',function(){
                ajaxCall('lpop',null)
            })
            $('#rpop').on('click',function(){
                ajaxCall('rpop',null)
            })
            $('#delete').on('click',function(){
                ajaxCall('del',null)
            })

        });
    </script>
</html>