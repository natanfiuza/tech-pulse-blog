<?php

namespace Tests\Unit;

use App\Services\ImagensDeConteudo;
use Tests\TestCase;

/**
 * A extração dos uuids é a base do diff de limpeza: se ela errar, ou sobra
 * arquivo no disco ou some imagem que ainda estava em uso.
 */
class ImagensDeConteudoTest extends TestCase
{
    private ImagensDeConteudo $servico;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servico = new ImagensDeConteudo;
    }

    public function test_extrai_o_uuid_de_uma_url_absoluta(): void
    {
        $uuid = '3f2504e0-4f89-11d3-9a0c-0305e82c3301';
        $conteudo = 'Texto antes ![foto](https://tech-pulse.natanfiuza.dev.br/post/content/images/meu-post/'
            .$uuid.') texto depois';

        $this->assertSame([$uuid], $this->servico->uuids_referenciados($conteudo));
    }

    public function test_extrai_o_uuid_de_uma_url_relativa(): void
    {
        $uuid = '3f2504e0-4f89-11d3-9a0c-0305e82c3301';
        $conteudo = '![foto](/post/content/images/meu-post/'.$uuid.')';

        $this->assertSame([$uuid], $this->servico->uuids_referenciados($conteudo));
    }

    public function test_extrai_o_uuid_independente_do_dominio(): void
    {
        // O domínio de produção pode mudar; a referência é o caminho da rota.
        $uuid = '3f2504e0-4f89-11d3-9a0c-0305e82c3301';
        $conteudo = '![foto](https://dominio-antigo.example.com/post/content/images/meu-post/'.$uuid.')';

        $this->assertSame([$uuid], $this->servico->uuids_referenciados($conteudo));
    }

    public function test_extrai_varios_uuids_sem_repetir(): void
    {
        $primeiro = '3f2504e0-4f89-11d3-9a0c-0305e82c3301';
        $segundo = '9c858901-8a57-4791-81fe-4c455b099bc9';
        $conteudo = '![a](/post/content/images/post-a/'.$primeiro.')';
        $conteudo .= "\n\n![b](/post/content/images/post-b/".$segundo.')';
        $conteudo .= "\n\n![a de novo](/post/content/images/post-a/".$primeiro.')';

        $this->assertSame(
            [$primeiro, $segundo],
            $this->servico->uuids_referenciados($conteudo)
        );
    }

    public function test_devolve_vazio_quando_nao_ha_imagem_de_conteudo(): void
    {
        $conteudo = "Só texto, sem imagem.\n\n![externa](https://exemplo.com/foto.png)";

        $this->assertSame([], $this->servico->uuids_referenciados($conteudo));
    }

    public function test_nao_extrai_uuid_solto_no_texto(): void
    {
        // Sem o caminho da rota não é uma imagem de conteúdo (nem que seja uuid).
        $conteudo = 'Um uuid qualquer: 3f2504e0-4f89-11d3-9a0c-0305e82c3301';

        $this->assertSame([], $this->servico->uuids_referenciados($conteudo));
    }

    public function test_devolve_vazio_para_conteudo_nulo_ou_branco(): void
    {
        $this->assertSame([], $this->servico->uuids_referenciados(null));
        $this->assertSame([], $this->servico->uuids_referenciados(''));
        $this->assertSame([], $this->servico->uuids_referenciados("  \n "));
    }

    public function test_normaliza_o_uuid_para_minusculas(): void
    {
        $conteudo = '![a](/post/content/images/post-a/3F2504E0-4F89-11D3-9A0C-0305E82C3301)';

        $this->assertSame(
            ['3f2504e0-4f89-11d3-9a0c-0305e82c3301'],
            $this->servico->uuids_referenciados($conteudo)
        );
    }

    public function test_monta_a_url_absoluta_com_o_slug_do_titulo(): void
    {
        // Mesmo slug que o Post usa: criar_slug() translitera e remove
        // preposições ("de" sai do título).
        $this->assertSame(
            config('techpulse.url_publica').'/post/content/images/meu-titulo-teste/3f2504e0-4f89-11d3-9a0c-0305e82c3301',
            $this->servico->url_absoluta('Meu Título de Teste', '3f2504e0-4f89-11d3-9a0c-0305e82c3301')
        );
    }

    public function test_usa_o_slug_de_fallback_quando_o_titulo_esta_vazio(): void
    {
        // Sem fallback a URL sairia com slug vazio e não casaria com a rota.
        $this->assertSame(
            config('techpulse.url_publica').'/post/content/images/sem-titulo/3f2504e0-4f89-11d3-9a0c-0305e82c3301',
            $this->servico->url_absoluta('', '3f2504e0-4f89-11d3-9a0c-0305e82c3301')
        );
    }
}
