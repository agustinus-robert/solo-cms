<div>
    <div class="row">
        <div class="col-md-12">
            <section>
                <div class="card border-0">
                    <div class="card-body">
                        <i class="mdi mdi-format-list-bulleted"></i> Menu Order
                    </div>
            </section>
        </div>

        <div class="col-md-12">
            <div class="card card-flush">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dd" id="nestable2" wire:ignore>
                                <ol class="dd-list">
                                    <?php

                                    if(gettype($data['order_menu']) == 'array' && count($data['order_menu']) > 0){
                                        foreach($data['order_menu'] as $index => $value){
                                            if(isset($value['id'])){
                                        ?>

                                    <li class="dd-item" data-id="<?= $value['id'] ?>">
                                        <div class="dd-handle">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <?= get_needed($value['id'])[0]->title ?></div>
                                                <div class="col-md-2 text-right">
                                                    <?php
                                                    if (get_needed($value['id'])[0]->type == '1') {
                                                        echo '<span class="badge badge-primary">' . 'general' . '</span>';
                                                    } elseif (get_needed($value['id'])[0]->type == '2') {
                                                        echo '<span class="badge badge-success">' . 'master menu' . '</span>';
                                                    } elseif (get_needed($value['id'])[0]->type == '3') {
                                                        echo '<span class="badge badge-warning">' . 'category' . '</span>';
                                                    } elseif (get_needed($value['id'])[0]->type == '5') {
                                                        echo '<span class="badge badge-secondary">' . 'Featured' . '</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(isset($value['children'])){ ?>
                                        <ol class="dd-list">
                                            <?php
                                            foreach($value['children'] as $index2 => $value2){  ?>
                                            <li class="dd-item" data-id="{{ $index2 }}">


                                                @if (is_array($value2))
                                                    <div class="dd-handle">
                                                        <div class="row">
                                                            <div class="col-md-10">
                                                                {{ isset(get_needed($index2)[0]) ? get_needed($index2)[0]->title : $index2 }}
                                                            </div>

                                                            <div class="col-md-2">
                                                                <?php
                                                                if (get_needed($index2)[0]->type == '1') {
                                                                    echo '<span class="badge badge-primary">' . 'general' . '</span>';
                                                                } elseif (get_needed($index2)[0]->type == '2') {
                                                                    echo '<span class="badge badge-success">' . 'master menu' . '</span>';
                                                                } elseif (get_needed($index2)[0]->type == '3') {
                                                                    echo '<span class="badge badge-warning">' . 'category' . '</span>';
                                                                } elseif (get_needed($index2)[0]->type == '5') {
                                                                    echo '<span class="badge badge-secondary">' . 'Featured' . '</span>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @foreach ($value2 as $index3 => $value3)
                                                        <ol class="dd-list">
                                                            <li class="dd-item" data-id="{{ $value3['id'] }}">
                                                                <div class="dd-handle">
                                                                    <div class="row">
                                                                        <div class="col-md-10">
                                                                            {{ get_needed($value3['id'])[0]->title }}
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                            <?php
                                                                            if (get_needed($value3['id'])[0]->type == '1') {
                                                                                echo '<span class="badge badge-primary">' . 'general' . '</span>';
                                                                            } elseif (get_needed($value3['id'])[0]->type == '2') {
                                                                                echo '<span class="badge badge-success">' . 'master menu' . '</span>';
                                                                            } elseif (get_needed($value3['id'])[0]->type == '3') {
                                                                                echo '<span class="badge badge-warning">' . 'category' . '</span>';
                                                                            } elseif (get_needed($value3['id'])[0]->type == '5') {
                                                                                echo '<span class="badge badge-secondary">' . 'Featured' . '</span>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ol>
                                                    @endforeach
                                                @else
                                                    <div class="dd-handle">
                                                        <div class="row">
                                                            <div class="col-md-10">
                                                                <?php

                                                                echo get_needed($value2)[0]->title;

                                                                ?>
                                                            </div>

                                                            <div class="col-md-2 text-right">
                                                                <?php
                                                                if (get_needed($value2)[0]->type == '1') {
                                                                    echo '<span class="badge badge-primary">' . 'general' . '</span>';
                                                                } elseif (get_needed($value2)[0]->type == '2') {
                                                                    echo '<span class="badge badge-success">' . 'master menu' . '</span>';
                                                                } elseif (get_needed($value2)[0]->type == '3') {
                                                                    echo '<span class="badge badge-warning">' . 'category' . '</span>';
                                                                } elseif (get_needed($value2)[0]->type == '5') {
                                                                    echo '<span class="badge badge-secondary">' . 'Featured' . '</span>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </li>
                                            <?php } ?>
                                        </ol>
                                        <?php }
                                        } ?>
                                    </li>
                                    <?php }
                                        }
                                    ?>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <textarea class="form-control" id="nestable2-output"></textarea>
                        </div>


                    </div>
                </div>

                <div class="card-footer text-center">
                    <input type="button" class="btn btn-primary" wire:click="submitForm" value="save">
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                var updateOutput = function(e) {
                    var list = e.length ? e : $(e.target),
                        output = list.data('output');
                    if (window.JSON) {
                        output.val(window.JSON.stringify(list.nestable('serialize')));
                        @this.set('order_menu', window.JSON.stringify(list.nestable('serialize')))
                    } else {
                        output.val('JSON browser support required for this demo.');
                    }
                };

                $('#nestable2').nestable({
                    group: 1
                }).on('change', updateOutput);

                updateOutput($('#nestable2').data('output', $('#nestable2-output')));

            })
        </script>
    </div>
</div>
