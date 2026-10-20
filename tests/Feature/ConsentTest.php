<?php

namespace Tests\Feature;

use App\Support\Consent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;
    public function test_nao_tem_decisao_enquanto_nao_ha_cookie(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('analytics.consent', null)
                ->where('analytics.cookie', 'consent')
                ->where('analytics.days', 180)
            );
    }

    public function test_le_a_decisao_do_cookie_aceita_ou_recusada(): void
    {
        foreach (Consent::values() as $decisao) {
            $this->withUnencryptedCookie('consent', $decisao)
                ->get('/')
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page->where('analytics.consent', $decisao));
        }
    }

    public function test_volta_a_sem_decisao_com_cookie_invalido_e_o_apaga(): void
    {
        $resposta = $this->withUnencryptedCookie('consent', 'invalido')
            ->get('/')
            ->assertOk();

        $resposta->assertInertia(fn (Assert $page) => $page->where('analytics.consent', null));
        $resposta->assertCookieExpired('consent');
    }

    public function test_nao_manda_cookie_de_limpeza_quando_a_decisao_e_valida(): void
    {
        $resposta = $this->withUnencryptedCookie('consent', Consent::DENIED)
            ->get('/')
            ->assertOk();

        $resposta->assertCookieMissing('consent');
    }

    public function test_so_manda_o_id_do_ga_quando_configurado(): void
    {
        config()->set('analytics.ga4_id', '');

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('analytics.ga4Id', null));

        config()->set('analytics.ga4_id', 'G-TESTE123');

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('analytics.ga4Id', 'G-TESTE123'));
    }

    public function test_trata_id_em_branco_como_medicao_desligada(): void
    {
        config()->set('analytics.ga4_id', '   ');

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('analytics.ga4Id', null));
    }

    public function test_nao_deixa_nenhuma_tag_de_medicao_no_html_do_servidor(): void
    {
        config()->set('analytics.ga4_id', 'G-TESTE123');

        $html = $this->withUnencryptedCookie('consent', Consent::GRANTED)
            ->get('/')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('googletagmanager', $html);
        $this->assertStringNotContainsString('dataLayer', $html);
        $this->assertStringNotContainsString('gtag(', $html);
    }
}
