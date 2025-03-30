<?php

namespace Danupe\Core\Classes;

class Table
{

    public array $options = [];
    public string $url = "";
    public bool $ajax = false;
    public array $data = [];

    public array $links = [];

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
        if ($this->ajax) {
            return $this->buildAjax();
        } else {
            return $this->buildHtml();
        }
    }

    private function buildHtml(): string
    {
        $html = "<div class='flex w-full overflow-x-auto'>
                <table class='table'>";

        $headers = array_keys($this->data[0]);

        if ($this->links) {
            $headers[] = "actions";
        }


        $html .= "<tr>";
        foreach ($headers as $header) {
            $html .= "<th>" . htmlspecialchars($header) . "</th>";
        }
        $html .= "</tr>";

        foreach ($this->data as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                $html .= "<td>" . htmlspecialchars($cell) . "</td>";
            }

            if ($this->links) {
                $html .= "<td>";
                foreach ($this->links as $key => $link) {
                    $url = danupe()->data()->get($link, 'url') . $row[danupe()->data()->get($link, 'key')];
                    $html .= "<a href='" . htmlspecialchars($url) . "' title='".$key."'><i class='" . danupe()->data()->get($link, 'icon') . "'></i></a> ";
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
        $html = "<div x-data='littleBIGtable({url: \'/" . $this->url . "\'})' x-init='init()'>";
        $html .= "<div class='flex w-full overflow-x-auto'>
                <table class='table'>";

        $headers = array_keys($this->data[0]);
        $html .= "<tr>";
        foreach ($headers as $header) {
            $html .= "<th>" . htmlspecialchars($header) . "</th>";
        }
        $html .= "</tr>";

        $html .= '<tbody>
                    <template x-for="row in rows">';
        $html .= '<tr>';
        foreach ($this->data[0] as $key => $cell) {
            $html .= '<td x-text="row.' . htmlspecialchars($key) . '"></td>';
        }
        $html .= '</tr>';
        $html .= '</template>
                </tbody>';

        $html .= "</table></div>";
        return $html;
    }
}
