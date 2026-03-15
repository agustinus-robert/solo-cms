<?php

namespace Modules\Cms\DataTables;

use Illuminate\Http\Request;
use Modules\Cms\Models\CmsPost;
use Modules\Cms\Models\CmsMenu;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use DB;

class PostingDatatables extends DataTable
{
    public function dataTable($query, Request $request)
    {
        $trash = $request->trash == 1 ? 1 : 0;

        $query->where('created_by', $request->user);

        return datatables()
            ->eloquent($query)
             ->addColumn('DT_RowIndex', function () {
                static $i = 0;
                return ++$i;
            })

            ->addColumn('title', function ($row) {
                return get_content_json($row)['title'] ?? '';
            })

            ->addColumn('slug', function ($row) {
                return get_content_json($row)['slug'] ?? '';
            })

            ->addColumn('created_at', function ($row) {
                return date('Y-m-d H:i:s', strtotime($row->created_at));
            })

            ->addColumn('action', function ($row) use ($trash) {

                if ($trash == 1) {
                    return '';
                }

                $menu_status = $row->menu;

                $template = '';

                if (empty($menu_status->edit) || $menu_status->edit == 1) {
                    $template .= view(
                        'cms::layouts_master.component.button_edit',
                        [
                            'id' => $row->id,
                            'update' => route(
                                'cms::builder.posting.edit',
                                ['posting' => '?id_menu=' . $row->menu_id . '&post_id=' . $row->id]
                            )
                        ]
                    )->render();
                }

                if (empty($menu_status->album) || $menu_status->album == 1) {
                    $template .= view(
                        'cms::layouts_master.component.button_image',
                        [
                            'id' => $row->id,
                            'btnimage' => route('cms::builder.posting_image.index') .
                                '?id_menu=' . $row->menu_id . '&post_id=' . $row->id
                        ]
                    )->render();
                }

                if (empty($menu_status->video) || $menu_status->video == 1) {
                    $template .= view(
                        'cms::layouts_master.component.button_video',
                        [
                            'id' => $row->id,
                            'btnvideo' => route('cms::builder.posting_video.index') .
                                '?id_menu=' . $row->menu_id . '&post_id=' . $row->id
                        ]
                    )->render();
                }

                if (empty($menu_status->delete) || $menu_status->delete == 1) {
                    $template .= view(
                        'cms::layouts_master.component.button_delete',
                        [
                            'id' => $row->id,
                            'delete' => route('cms::builder.posting.destroy', ['posting' => $row->id])
                        ]
                    )->render();
                }

                return $template;
            })

            ->addColumn('status', function ($row) {

                $text = '';
                $status_now = 0;

                $sch = $row->schedule;

                if (
                    isset($sch->schedule_on)
                    && empty($sch->deleted_at)
                    && now()->format('Y-m-d H:i') <
                    date('Y-m-d H:i', strtotime($sch->schedule_on . ' ' . $sch->timepicker))
                ) {

                    $text = 'Post soon publish';

                } elseif ($row->status == 2) {

                    $text = 'Publish';
                    $status_now = 2;

                } elseif ($row->status == 3) {

                    $text = 'Draft';
                    $status_now = 3;
                }

                return view(
                    'cms::layouts_master.component.button_status_edit',
                    [
                        'id' => $row->id,
                        'status' => $status_now,
                        'sch_post' => $sch
                    ]
                )->render() . $text;
            })

            ->rawColumns(['status', 'action']);
    }


    public function query(CmsPost $model, Request $request)
    {
        $str = $request->header('referer');
        $qs = parse_url($str, PHP_URL_QUERY);

        if (!empty($qs)) {

            parse_str($qs, $output);

            return $this->applyScopes(
                $model->with(['menu', 'schedule'])
                    ->where('menu_id', $output['id_menu'])
                    ->whereNull('deleted_at')
            );
        }

        return $this->applyScopes(
            $model->with(['menu', 'schedule'])
        );
    }


    public function html()
    {
        return $this->builder()
            ->setTableId('datatablesSimple')
            ->columns($this->getColumns())
            ->minifiedAjax('datatable?class=' . __CLASS__)
            ->drawCallbackWithLivewire()
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            )
            ->parameters([
                'paging' => true,
                'searching' => true,
                'info' => false,
                'searchDelay' => 350,
            ]);
    }


    protected function getColumns()
    {
        return [

            Column::computed('DT_RowIndex')
                ->title('No')
                ->orderable(false)
                ->searchable(false),

            Column::make('title'),

            Column::make('slug'),

            Column::make('created_at'),

            Column::make('status')
                ->orderable(false)
                ->searchable(false),

            Column::make('action')
                ->orderable(false)
                ->searchable(false)
        ];
    }
}
