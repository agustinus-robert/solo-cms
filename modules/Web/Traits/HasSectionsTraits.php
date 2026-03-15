<?php

namespace Modules\Web\Traits;

trait HasSectionsTraits
{
    protected array $pageSections = [];

    /**
     * Menambahkan banyak section sekaligus dengan urutan
     * Format: 'view_path' => ['data' => [...], 'order' => 1]
     */
    public function setSections(array $sections): self
    {
        foreach ($sections as $view => $config) {
            $order = $config['order'] ?? 99;
            $data = $config['data'] ?? $config;

            $this->addSection($view, $data, $order);
        }

        return $this;
    }

    /**
     * Tambah satu section dengan order
     */
    public function addSection(string $view, array $data = [], int $order = 99): self
    {
        $this->pageSections[] = [
            'view'  => $view,
            'data'  => $data,
            'order' => $order
        ];

        return $this;
    }

    /**
     * Mengambil section yang sudah diurutkan
     */
    public function getPageSections(): array
    {
        // Mengurutkan berdasarkan 'order' terkecil ke terbesar
        return collect($this->pageSections)->sortBy('order')->values()->all();
    }
}
