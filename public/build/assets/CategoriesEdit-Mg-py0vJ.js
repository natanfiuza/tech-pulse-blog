import{d as m,c as i,o as a,a as g,b as t,w as f,u as o,e as d,f as p,v as u,t as n,g as _,F as v,r as y,h as k}from"./app-Bwqwfv7a.js";import{_ as x}from"./_plugin-vue_export-helper-DlAUqK2U.js";const C={class:"mb-3"},V={key:0,class:"invalid-feedback"},U={class:"mb-3"},E={key:0,class:"invalid-feedback"},N={class:"mb-3"},P={key:0,class:"invalid-feedback"},S={class:"mb-3"},w={key:0,class:"invalid-feedback"},A={class:"mb-3"},B={key:0,class:"invalid-feedback"},D={class:"mb-3"},F=["value"],M={key:0,class:"invalid-feedback"},T=["disabled"],j={__name:"CategoriesEdit",props:{category:Object,categories:Array},setup(c){const l=c,e=m.useForm({name:l.category.name,description:l.category.description,scope:l.category.scope,possible_contents:l.category.possible_contents,post_suggestions:l.category.post_suggestions,parent_id:l.category.parent_id,_method:"PUT"}),b=()=>{e.put(route("categories.update",{category:l.category.slug}),{preserveScroll:!0})};return(q,s)=>(a(),i("div",null,[g(o(m.Head),null,{default:f(()=>s[6]||(s[6]=[t("title",null,"Editar Categoria",-1)])),_:1}),s[14]||(s[14]=t("h1",null,"Editar Categoria",-1)),t("form",{onSubmit:k(b,["prevent"])},[t("div",C,[s[7]||(s[7]=t("label",{for:"name",class:"form-label"},"Nome:",-1)),d(t("input",{type:"text",id:"name","onUpdate:modelValue":s[0]||(s[0]=r=>o(e).name=r),class:"form-control",required:""},null,512),[[u,o(e).name]]),o(e).errors.name?(a(),i("div",V,n(o(e).errors.name),1)):p("",!0)]),t("div",U,[s[8]||(s[8]=t("label",{for:"description",class:"form-label"},"Descrição:",-1)),d(t("textarea",{id:"description","onUpdate:modelValue":s[1]||(s[1]=r=>o(e).description=r),class:"form-control"},null,512),[[u,o(e).description]]),o(e).errors.description?(a(),i("div",E,n(o(e).errors.description),1)):p("",!0)]),t("div",N,[s[9]||(s[9]=t("label",{for:"scope",class:"form-label"},"Abrangência:",-1)),d(t("textarea",{id:"scope","onUpdate:modelValue":s[2]||(s[2]=r=>o(e).scope=r),class:"form-control"},null,512),[[u,o(e).scope]]),o(e).errors.scope?(a(),i("div",P,n(o(e).errors.scope),1)):p("",!0)]),t("div",S,[s[10]||(s[10]=t("label",{for:"possible_contents",class:"form-label"},"Possíveis Conteúdos:",-1)),d(t("textarea",{id:"possible_contents","onUpdate:modelValue":s[3]||(s[3]=r=>o(e).possible_contents=r),class:"form-control"},null,512),[[u,o(e).possible_contents]]),o(e).errors.possible_contents?(a(),i("div",w,n(o(e).errors.possible_contents),1)):p("",!0)]),t("div",A,[s[11]||(s[11]=t("label",{for:"post_suggestions",class:"form-label"},"Sugestões de Postagens:",-1)),d(t("textarea",{id:"post_suggestions","onUpdate:modelValue":s[4]||(s[4]=r=>o(e).post_suggestions=r),class:"form-control"},null,512),[[u,o(e).post_suggestions]]),o(e).errors.post_suggestions?(a(),i("div",B,n(o(e).errors.post_suggestions),1)):p("",!0)]),t("div",D,[s[13]||(s[13]=t("label",{for:"parent_id",class:"form-label"},"Categoria Pai:",-1)),d(t("select",{id:"parent_id","onUpdate:modelValue":s[5]||(s[5]=r=>o(e).parent_id=r),class:"form-select"},[s[12]||(s[12]=t("option",{value:null},"Nenhuma (Categoria Principal)",-1)),(a(!0),i(v,null,y(c.categories,r=>(a(),i("option",{key:r.id,value:r.id},n(r.name),9,F))),128))],512),[[_,o(e).parent_id]]),o(e).errors.parent_id?(a(),i("div",M,n(o(e).errors.parent_id),1)):p("",!0)]),t("button",{type:"submit",class:"btn btn-primary",disabled:o(e).processing}," Atualizar ",8,T)],32)]))}},I=x(j,[["__scopeId","data-v-9fcd9b3d"]]);export{I as default};
