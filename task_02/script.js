const data=[{name:'Arun',department:'CSE',date:'2026-04-10'},{name:'Bala',department:'ECE',date:'2026-04-11'},{name:'Chitra',department:'CSE',date:'2026-04-09'}];
function render(){let rows=[...data];const sortBy=document.getElementById('sortBy').value;const dept=document.getElementById('deptFilter').value;
if(dept!=='all') rows=rows.filter(r=>r.department===dept);
rows.sort((a,b)=>sortBy==='name'?a.name.localeCompare(b.name):a.date.localeCompare(b.date));
document.getElementById('rows').innerHTML=rows.map(r=>`<tr><td>${r.name}</td><td>${r.department}</td><td>${r.date}</td></tr>`).join('');
const counts=rows.reduce((acc,r)=>(acc[r.department]=(acc[r.department]||0)+1,acc),{});
document.getElementById('countBox').innerHTML=Object.entries(counts).map(([k,v])=>`<p>${k}: ${v}</p>`).join('');}
render();