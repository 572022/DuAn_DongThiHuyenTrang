using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Web;
using WebMULTI.Models;

namespace WebMULTI.Areas.Admin.Models
{
    public class ListColor
    {
        webMultiDataContext db = new webMultiDataContext();
        public string nameColor { get; set; }
        public int idColor { get; set; }
        public int idColor2 { get; set; }

        public int MaSP { get; set; }
        public string Ecolor { get; set; }
        public string Ecolor2 { get; set; }
        public ListColor(int color,int color2)
        {

            idColor = color;
            idColor2 = color2;
        }
    }
}