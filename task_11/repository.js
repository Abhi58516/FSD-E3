const data=[{id:1,name:'A',department:'CSE',age:20},{id:2,name:'B',department:'ECE',age:22},{id:3,name:'C',department:'CSE',age:21}];
function findByDepartment(department,page=1,size=2){const filtered=data.filter(s=>s.department===department);const start=(page-1)*size;return filtered.slice(start,start+size);} 
function findByAgeGreaterThan(age){return data.filter(s=>s.age>age).sort((a,b)=>a.age-b.age);} 
module.exports={findByDepartment,findByAgeGreaterThan};