using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using PagedList;
using PagedList.Mvc;
namespace WebMULTI.Models
{
    public class Procom
    {
        public pro_color procductDetails { get; set; }
        public IPagedList<BinhLuan> Comments { get; set; }
        //img
        public List<Productimg> Productimg { get; set; }
 


    }
}