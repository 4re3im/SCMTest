<?php
defined('C5_EXECUTE') or die(_("Acess Denied"));
?>
<aside class="main-sidebar">

    <section class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="image">
                <img src="images/cover.jpg" alt="Book Cover">
            </div>
            <div class="info">
                <h4>Developing Game Sense through Tactical Learning</h4>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu tabs list-group">
            <li class="header">TABS</li>
            <div><a type="button" class="btn btn-primary btn-xs">Add</a></div>
            <form class="sidebar-form" method="get" action="#">
                <div class="input-group">
                    <input type="text" placeholder="Filter..." class="form-control" name="q">
                    <span class="input-group-btn">
                        <button class="btn btn-flat" id="search-btn" name="search" type="submit"><i class="fa fa-filter"></i>
                        </button>
                    </span>
                </div>
            </form>
            <li>
                <a href="7-1-tabs.html"><i class="fa fa-columns"></i> Essential Mathematics VELS Edition Year 10 Gold</a>
                <small class="tabEdit label bg-blue"><a href="7.2b-tab-contents.html">EDIT</a></small>
            </li>
            <li>
                <a href="7-1-tabs.html"><i class="fa fa-columns"></i> Mother Link Developing Game Sense through Tactical Learning</a>
                <small class="tabEdit label bg-blue"><a href="7.2b-tab-contents.html">EDIT</a></small>
            </li>
        </ul>
    </section>
</aside>

<form role="form">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">

            <div class="save">
                <button type="button" class="btn btn-dark">Archive</button>
                <button type="button" class="btn btn-success">Save</button>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-4 col-lg-offset-1">


                    <!-- ############# -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Information</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>

                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="tabname">Tab Name</label>
                                                <input type="text" placeholder="Enter tab name" id="tabname" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                            <div class="form-group">
                                                <label for="tabname">Columns</label>
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="pill">

                                                            <div id="tab" class="btn-group" data-toggle="buttons">
                                                                <a href="#" class="btn btn-default" data-toggle="tab">
                                                                    <input type="radio" /> 1</a>
                                                                <a href="#" class="btn btn-default" data-toggle="tab">
                                                                    <input type="radio" /> 2</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Access Rights</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>

                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="tabname">Active</label>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="tab-active"
                                                           checked>
                                                    <label class="onoffswitch-label" for="tab-active">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group tab-icon">
                                                <label>Tab Icon</label>
                                                <select style="font-family:'FontAwesome', Arial;" class="form-control select2" style="width: 100%;">
                                                    <option>&#xf187;</option>
                                                    <option>&#xf1ba;</option>
                                                    <option>&#xf0e7;</option>
                                                    <option>&#xf1d0;</option>
                                                    <option>&#xf0e4;</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="tabname">Visibility</label>
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="pill">
                                                            <div id="content-visibility" class="btn-group" data-toggle="buttons">
                                                                <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                                    <input type="radio" /> Public</a>
                                                                <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                                    <input type="radio" /> Private</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="tabname">Access to Tab</label>
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="pill">
                                                            <div id="access-to-tab" class="btn-group" data-toggle="buttons">
                                                                <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                                    <input type="radio" /> Student</a>
                                                                <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                                    <input type="radio" /> Teacher</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="tabname">Content Access</label>
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="pill">
                                                            <div id="content-access" class="btn-group" data-toggle="buttons">
                                                                <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                                    <input type="radio" /> Free</a>
                                                                <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                                    <input type="radio" /> Login Only</a>
                                                                <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                                    <input type="radio" /> Subscription</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="tab-message">Custom Message</label>
                                                <textarea class="form-control" id="tab-message" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6">


                    <!-- ############# -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Options</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="tabname">My Resources Link</label>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"
                                                           checked>
                                                    <label class="onoffswitch-label" for="myonoffswitch">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="resource">Resource URL</label>
                                                <input type="text" placeholder="Enter resource URL" id="resource" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Text</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>

                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="tabname">Always Use Public Text?</label>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="public-text"
                                                           checked>
                                                    <label class="onoffswitch-label" for="public-text">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Public Tab Text</label>

                                                <textarea id="public-tab-text" name="editor1" rows="10" cols="80">
                                This is my textarea to be replaced with CKEditor.
                                                </textarea>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Private Tab Text</label>

                                                <textarea id="private-tab-text" name="editor2" rows="10" cols="80">
                                This is my textarea to be replaced with CKEditor.
                                                </textarea>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            </form>

