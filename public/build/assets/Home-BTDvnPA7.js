import{e as l,o,g as r,a as e,w as i,u as n,i as c,t as d,h as m,s as _,f as g,F as h,l as v,c as x}from"./app-BNi4kNQT.js";/* empty css             */import{N as k}from"./Navbar-_Q45umiw.js";import{_ as N}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                               */const w={class:"post-card"},L=["src","alt"],y={__name:"PostCard",props:{post:{type:Object,required:!0}},setup(t){return(p,s)=>(o(),l("div",w,[r(n(c.InertiaLink),{href:"/post/show/"+t.post.slug},{default:i(()=>[e("img",{src:`${t.post.image}`,alt:t.post.title},null,8,L)]),_:1},8,["href"]),r(n(c.InertiaLink),{href:"/post/show/"+t.post.slug},{default:i(()=>[e("h3",null,d(t.post.title),1)]),_:1},8,["href"]),e("p",null,d(t.post.excerpt),1),r(n(c.InertiaLink),{href:"/post/show/"+t.post.slug,class:""},{default:i(()=>s[0]||(s[0]=[m("Leia Mais")])),_:1},8,["href"])]))}},I=N(y,[["__scopeId","data-v-8d87ce5a"]]),b={class:"hero"},B={class:"hero-content"},C={key:0,class:"hero-featured"},V=["src","alt"],$={class:"featured-details"},E={class:"featured-title"},F={class:"featured-excerpt"},P={class:"featured-posts",id:"cards_home"},A={__name:"Home",props:{posts:Array},setup(t){const p=t,s=_(()=>{let u=null;return p.posts.map(a=>{a.featured_post&&(u=a)}),u});return(u,a)=>(o(),l(h,null,[r(k),e("section",b,[e("div",B,[a[0]||(a[0]=e("div",{class:"hero-text"},[e("h1",null,"Descubra o Futuro da Tecnologia"),e("p",null," Explore os últimos insights, tendências e inovações no mundo da tecnologia. Nosso blog traz análises profundas e conteúdo de qualidade para entusiastas e profissionais. ")],-1)),s.value?(o(),l("div",C,[r(n(c.InertiaLink),{href:"/post/show/"+s.value.slug,class:"featured-link"},{default:i(()=>[e("img",{src:s.value.image,alt:s.value.title,class:"featured-image"},null,8,V),e("div",$,[e("h2",E,d(s.value.title),1),e("p",F,d(s.value.excerpt),1)])]),_:1},8,["href"])])):g("",!0)])]),e("section",P,[(o(!0),l(h,null,v(t.posts,f=>(o(),x(I,{key:f.id,post:f},null,8,["post"]))),128))])],64))}};export{A as default};
