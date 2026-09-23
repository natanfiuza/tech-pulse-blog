import { mount } from '@vue/test-utils'
import ModalCategoriasGuia from '../../../resources/js/Components/Admin/ModalCategoriasGuia.vue'

const sampleCategorias = [
  {
    id: 1,
    name: 'Raiz',
    description: '*texto em itálico*',
    scope: 'Escopo **negrito**',
    possible_contents: 'Conteúdo `code`',
    post_suggestions: 'Sugestão **Importante**',
    children: [
      {
        id: 2,
        name: 'Filho',
        description: 'Filho *markdown*',
        scope: 'Filho scope',
        possible_contents: 'Filho conteúdo',
        post_suggestions: 'Filho sugestão',
        children: []
      }
    ]
  }
]

describe('ModalCategoriasGuia.vue', () => {
  test('renders labels in bold and parses markdown', async () => {
    const wrapper = mount(ModalCategoriasGuia, {
      props: { aberto: true, categorias: sampleCategorias },
      global: {
        stubs: {
          Teleport: true,
          Transition: false
        }
      }
    })
    await wrapper.vm.$nextTick()

    // Expand root by clicking first toggle element
    const rootToggle = wrapper.find('.flex.items-center.justify-between.cursor-pointer')
    expect(rootToggle.exists()).toBe(true)
    await rootToggle.trigger('click')
    await wrapper.vm.$nextTick()

    const strongs = wrapper.findAll('strong')
    expect(strongs).toHaveLength(8) // 4 root + 4 child labels
    const expectedLabels = [
      'Descrição:',
      'Abrangência:',
      'Possíveis Conteúdos:',
      'Sugestões de Postagens:',
      'Descrição:',
      'Abrangência:',
      'Possíveis Conteúdos:',
      'Sugestões de Postagens:'
    ]
    expectedLabels.forEach(label => {
      expect(strongs.filter(w => w.text() === label).length).toBeGreaterThan(0)
    })

    // Verify markdown conversion – the description uses <em> for italics
    expect(wrapper.html()).toContain('<em>texto em itálico</em>')
    expect(wrapper.html()).toContain('<em>markdown</em>')
  })

  test('renders hierarchical structure (root and child)', async () => {
    const wrapper = mount(ModalCategoriasGuia, {
      props: { aberto: true, categorias: sampleCategorias },
      global: {
        stubs: {
          Teleport: true,
          Transition: false
        }
      }
    })
    await wrapper.vm.$nextTick()

    const rootToggle = wrapper.find('.flex.items-center.justify-between.cursor-pointer')
    expect(rootToggle.exists()).toBe(true)
    await rootToggle.trigger('click')
    await wrapper.vm.$nextTick()

    // Child name should appear after expansion
    expect(wrapper.text()).toContain('Raiz')
    expect(wrapper.text()).toContain('Filho')
    const childContainer = wrapper.find('.border-l')
    expect(childContainer.exists()).toBeTruthy()
  })
})
