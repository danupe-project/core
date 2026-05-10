<?php

namespace Danupe\Core\Classes;

class Table
{
    public array $options = [];
    public string $url = "";
    public bool $ajax = false;
    public array $data = [];
    public array $links = [];
    /**
     * NEU: Definition der Spalten
     * Struktur: 'key' => [
     * 'label' => 'Header', 
     * 'formatter' => function($val, $row) { return ... }, 
     * 'template' => '<span>...</span>' (Alpine.js HTML)
     * ]
     */
    public array $columns = [];

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    public function setAjax(bool $ajax): self
    {
        $this->ajax = $ajax;
        return $this;
    }

    public function setData(array $data = []): self
    {
        $this->data = $data;
        return $this;
    }

    public function setOptions(array $options = []): self
    {
        $this->options = $options;
        return $this;
    }

    public function setUrl(string $url = ""): self
    {
        $this->url = $url;
        return $this;
    }

    public function render(): string
    {
        return $this->ajax ? $this->buildAjax() : $this->buildHtml();
    }

    private function buildHtml(): string
    {
        if (empty($this->data)) {
            return danupe()->view()->render('core', 'components/alert', [
                'type' => 'warning',
                'title' => 'Hinweis',
                'text' => 'Keine Daten gefunden',
            ]);
        }

        // Header ermitteln (entweder aus columns oder aus den Daten-Keys)
        $headers = !empty($this->columns) ? array_column($this->columns, 'label') : array_keys($this->data[0]);
        if ($this->links)
            $headers[] = "Aktionen";

        $html = "<div class='flex w-full overflow-x-auto'><table class='table'>";

        // Header Zeile
        $html .= "<tr>";
        foreach ($headers as $header) {
            $html .= "<th>" . htmlspecialchars($header) . "</th>";
        }
        $html .= "</tr>";

        // Daten Zeilen
        foreach ($this->data as $row) {
            $html .= "<tr>";

            if (!empty($this->columns)) {
                foreach ($this->columns as $key => $col) {
                    $value = $row[$key] ?? '';
                    // Falls ein Formatter existiert, nutze ihn, sonst Text-Ausgabe
                    if (isset($col['formatter']) && is_callable($col['formatter'])) {
                        $displayValue = $col['formatter']($value, $row);
                    } else {
                        $displayValue = htmlspecialchars((string) $value);
                    }
                    $html .= "<td>$displayValue</td>";
                }
            } else {
                // Fallback: Alle Felder aus dem Row-Array
                foreach ($row as $cell) {
                    $html .= "<td>" . htmlspecialchars((string) $cell) . "</td>";
                }
            }

            // Links/Aktionen
            if ($this->links) {
                $html .= "<td>";
                foreach ($this->links as $key => $link) {
                    $url = danupe()->data()->get($link, 'url') . $row[danupe()->data()->get($link, 'key')];
                    $html .= "<a href='" . htmlspecialchars($url) . "' title='" . $key . "' style='margin-right:8px;'><i class='" . danupe()->data()->get($link, 'icon') . "'></i></a> ";
                }
                $html .= "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table></div>";
        return $html;
    }

    private function buildAjax(): string
    {
        // Spalten-Keys für das Template-Loop
        $keys = !empty($this->columns) ? array_keys($this->columns) : [];
        if (empty($keys) && !empty($this->data)) {
            $keys = array_keys($this->data[0]);
        }

        $pageSizeOptions = $this->options['page_sizes'] ?? [10, 25, 50, 100];
        $searchPlaceholder = $this->options['search_placeholder'] ?? 'Suche...';
        $noDataText = $this->options['no_data'] ?? 'Keine Daten';

        $html = "<div x-data=\"littleBIGtable({url: '/" . $this->url . "'})\" x-init=\"init()\">";

        // Controls
        $html .= "<div class='table-controls flex flex-wrap gap-2 items-center mb-4'>";
        $html .= "<input type='text' class='input' style='max-width:250px' placeholder='" . htmlspecialchars($searchPlaceholder) . "' x-model=\"params.search\" @input.debounce.500ms=\"doSearch()\" />";
        $html .= "<select class='select' style='width:auto' x-model=\"params.limit\" @change=\"setLimit()\">";
        foreach ($pageSizeOptions as $opt) {
            $html .= "<option value='$opt'>$opt</option>";
        }
        $html .= "</select>";
        $html .= "<div class='flex items-center gap-2 ml-auto'>";
        $html .= "<button type='button' class='btn btn-xs' @click=\"goPrevPage()\" :disabled=\"params.offset == 0\">&lsaquo;</button>";
        $html .= "<span class='text-sm' x-text=\"Math.floor(params.offset/params.limit)+1\"></span>";
        $html .= "<button type='button' class='btn btn-xs' @click=\"goNextPage()\" :disabled=\"rows.length < params.limit\">&rsaquo;</button>";
        $html .= "</div>";
        $html .= "</div>";

        $html .= "<div class='flex w-full overflow-x-auto'><table class='table'>";

        // Header
        $html .= "<thead><tr>";
        foreach ($keys as $key) {
            $label = $this->columns[$key]['label'] ?? $key;
            $html .= "<th class='cursor-pointer select-none' @click=\"doSort('$key')\">" . htmlspecialchars($label) . " <span x-html=\"getSortIcon('$key')\"></span></th>";
        }
        if ($this->links)
            $html .= "<th>Aktionen</th>";
        $html .= "</tr></thead>";

        // Body mit Alpine Template
        $html .= "<tbody>";
        $html .= "<template x-if=\"!rows.length && !meta.loading\"><tr><td colspan='20' class='text-center italic'>" . htmlspecialchars($noDataText) . "</td></tr></template>";
        $html .= "<template x-for=\"row in rows\" :key=\"row.id\">";
        $html .= "<tr>";

        foreach ($keys as $key) {
            if (isset($this->columns[$key]['template'])) {
                $template = $this->columns[$key]['template'];
                // Wir nutzen hier ' für das Attribut, damit " im Template erlaubt ist
                $html .= "<td x-html='" . $template . "'></td>";
            } else {
                $html .= "<td x-text=\"row.$key\"></td>";
            }
        }

        // Aktionen (Links)
        if ($this->links) {
            $html .= "<td>";
            foreach ($this->links as $linkKey => $link) {
                $icon = htmlspecialchars(danupe()->data()->get($link, 'icon', 'fas fa-edit'));
                $urlBase = htmlspecialchars(danupe()->data()->get($link, 'url', ''));
                $rowKey = htmlspecialchars(danupe()->data()->get($link, 'key', 'id'));
                $html .= "<a class='px-1 text-primary' :href=\"'$urlBase' + row.$rowKey\" title='" . htmlspecialchars($linkKey) . "'><i class='$icon'></i></a> ";
            }
            $html .= "</td>";
        }

        $html .= "</tr>";
        $html .= "</template>";
        $html .= "</tbody>";

        $html .= "</table></div>";
        $html .= "</div>";

        return $html;
    }
}