using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using WebsiteQuanAo.Models;
namespace WebsiteQuanAo.Models
{
    public class Product1
    {
        public int ProductId { get; set; }
        public string AnhBia { get; set; }
        // Thêm các thuộc tính khác của sản phẩm
    }

    public class Comment1
    {
        public int id { get; set; }
        public string Username { get; set; }
        public string danhgia { get; set; }
        public string AnhBia { get; set; }

        // Thêm các thuộc tính khác của bình luận
    }
    public class ProductCommentViewModel
    {
        public int id { get; set; }
        public string Anhbia { get; set; }
        public string CommentText { get; set; }
        public string User { get; set; }
        public string tensp { get; set; }
        public DateTime Ngaybl { get; set;}
    }


}