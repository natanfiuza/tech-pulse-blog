import{p as V}from"./chunk-4BMEZGHF-D5Cz3pex.js";import{Q as S,T as F,aF as U,_ as p,g as j,s as q,a as H,b as Q,q as Z,p as J,l as z,c as K,D as X,H as Y,a4 as tt,e as et,y as at,F as rt}from"./Post-u2-QLEwT.js";import{p as nt}from"./radar-MK3ICKWK--wQUXiaj.js";import{d as P}from"./arc-CmnlGUjc.js";import{o as it}from"./ordinal-Cboi1Yqb.js";import"./app-BAUXFxLy.js";import"./datetime-BDQ5ptpc.js";import"./index-BMSQkwth.js";import"./helpers-Cyb95aEE.js";import"./Navbar-C6l6LtfT.js";import"./use_theme--1S536dh.js";import"./Footer-BfC8dcoE.js";import"./HashtagChip-Cf_W5WD6.js";import"./SidebarPanel-nH1V3G5R.js";import"./ModalConfirmacao-BeBCHjYj.js";import"./NewsletterBox-B9mgSkHk.js";import"./InputError-CFuyBle0.js";import"./_baseUniq-DXSiIDx4.js";import"./_basePickBy-CdqxVxC8.js";import"./clone-BmKntJVY.js";import"./init-Gi6I4Gst.js";function ot(t,a){return a<t?-1:a>t?1:a>=t?0:NaN}function st(t){return t}function lt(){var t=st,a=ot,m=null,s=S(0),u=S(F),x=S(0);function i(e){var r,l=(e=U(e)).length,g,A,h=0,c=new Array(l),n=new Array(l),v=+s.apply(this,arguments),w=Math.min(F,Math.max(-F,u.apply(this,arguments)-v)),f,T=Math.min(Math.abs(w)/l,x.apply(this,arguments)),$=T*(w<0?-1:1),d;for(r=0;r<l;++r)(d=n[c[r]=r]=+t(e[r],r,e))>0&&(h+=d);for(a!=null?c.sort(function(y,D){return a(n[y],n[D])}):m!=null&&c.sort(function(y,D){return m(e[y],e[D])}),r=0,A=h?(w-l*$)/h:0;r<l;++r,v=f)g=c[r],d=n[g],f=v+(d>0?d*A:0)+$,n[g]={data:e[g],index:r,value:d,startAngle:v,endAngle:f,padAngle:T};return n}return i.value=function(e){return arguments.length?(t=typeof e=="function"?e:S(+e),i):t},i.sortValues=function(e){return arguments.length?(a=e,m=null,i):a},i.sort=function(e){return arguments.length?(m=e,a=null,i):m},i.startAngle=function(e){return arguments.length?(s=typeof e=="function"?e:S(+e),i):s},i.endAngle=function(e){return arguments.length?(u=typeof e=="function"?e:S(+e),i):u},i.padAngle=function(e){return arguments.length?(x=typeof e=="function"?e:S(+e),i):x},i}var ct=rt.pie,G={sections:new Map,showData:!1},b=G.sections,W=G.showData,pt=structuredClone(ct),ut=p(()=>structuredClone(pt),"getConfig"),gt=p(()=>{b=new Map,W=G.showData,at()},"clear"),dt=p(({label:t,value:a})=>{b.has(t)||(b.set(t,a),z.debug(`added new section: ${t}, with value: ${a}`))},"addSection"),ft=p(()=>b,"getSections"),mt=p(t=>{W=t},"setShowData"),ht=p(()=>W,"getShowData"),R={getConfig:ut,clear:gt,setDiagramTitle:J,getDiagramTitle:Z,setAccTitle:Q,getAccTitle:H,setAccDescription:q,getAccDescription:j,addSection:dt,getSections:ft,setShowData:mt,getShowData:ht},vt=p((t,a)=>{V(t,a),a.setShowData(t.showData),t.sections.map(a.addSection)},"populateDb"),yt={parse:p(async t=>{const a=await nt("pie",t);z.debug(a),vt(a,R)},"parse")},St=p(t=>`
  .pieCircle{
    stroke: ${t.pieStrokeColor};
    stroke-width : ${t.pieStrokeWidth};
    opacity : ${t.pieOpacity};
  }
  .pieOuterCircle{
    stroke: ${t.pieOuterStrokeColor};
    stroke-width: ${t.pieOuterStrokeWidth};
    fill: none;
  }
  .pieTitleText {
    text-anchor: middle;
    font-size: ${t.pieTitleTextSize};
    fill: ${t.pieTitleTextColor};
    font-family: ${t.fontFamily};
  }
  .slice {
    font-family: ${t.fontFamily};
    fill: ${t.pieSectionTextColor};
    font-size:${t.pieSectionTextSize};
    // fill: white;
  }
  .legend text {
    fill: ${t.pieLegendTextColor};
    font-family: ${t.fontFamily};
    font-size: ${t.pieLegendTextSize};
  }
`,"getStyles"),xt=St,At=p(t=>{const a=[...t.entries()].map(s=>({label:s[0],value:s[1]})).sort((s,u)=>u.value-s.value);return lt().value(s=>s.value)(a)},"createPieArcs"),wt=p((t,a,m,s)=>{z.debug(`rendering pie chart
`+t);const u=s.db,x=K(),i=X(u.getConfig(),x.pie),e=40,r=18,l=4,g=450,A=g,h=Y(a),c=h.append("g");c.attr("transform","translate("+A/2+","+g/2+")");const{themeVariables:n}=x;let[v]=tt(n.pieOuterStrokeWidth);v??(v=2);const w=i.textPosition,f=Math.min(A,g)/2-e,T=P().innerRadius(0).outerRadius(f),$=P().innerRadius(f*w).outerRadius(f*w);c.append("circle").attr("cx",0).attr("cy",0).attr("r",f+v/2).attr("class","pieOuterCircle");const d=u.getSections(),y=At(d),D=[n.pie1,n.pie2,n.pie3,n.pie4,n.pie5,n.pie6,n.pie7,n.pie8,n.pie9,n.pie10,n.pie11,n.pie12],C=it(D);c.selectAll("mySlices").data(y).enter().append("path").attr("d",T).attr("fill",o=>C(o.data.label)).attr("class","pieCircle");let N=0;d.forEach(o=>{N+=o}),c.selectAll("mySlices").data(y).enter().append("text").text(o=>(o.data.value/N*100).toFixed(0)+"%").attr("transform",o=>"translate("+$.centroid(o)+")").style("text-anchor","middle").attr("class","slice"),c.append("text").text(u.getDiagramTitle()).attr("x",0).attr("y",-400/2).attr("class","pieTitleText");const M=c.selectAll(".legend").data(C.domain()).enter().append("g").attr("class","legend").attr("transform",(o,k)=>{const E=r+l,L=E*C.domain().length/2,_=12*r,B=k*E-L;return"translate("+_+","+B+")"});M.append("rect").attr("width",r).attr("height",r).style("fill",C).style("stroke",C),M.data(y).append("text").attr("x",r+l).attr("y",r-l).text(o=>{const{label:k,value:E}=o.data;return u.getShowData()?`${k} [${E}]`:k});const I=Math.max(...M.selectAll("text").nodes().map(o=>(o==null?void 0:o.getBoundingClientRect().width)??0)),O=A+e+r+l+I;h.attr("viewBox",`0 0 ${O} ${g}`),et(h,g,O,i.useMaxWidth)},"draw"),Dt={draw:wt},jt={parser:yt,db:R,renderer:Dt,styles:xt};export{jt as diagram};
