using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Web;
using System.Web.Mvc;

namespace WebsiteQuanAo.Models
{
    //public class Item
    // {
    //     public int Id { get; set; }
    //     public string Name { get; set; }
    // }
    public class MyModel
    {
        public int SelectedItemId { get; set; }
        public List<SelectListItem> Items { get; set; }
    }

}