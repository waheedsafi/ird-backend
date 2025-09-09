   <table width="100%" style="border-collapse: collapse; font-size: 13px; margin-top: 20px;">
       <!-- Subject -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black; width: 35%;">Organization
           </td>
           <td style="padding: 6px; border: 1px solid black">{{ $subject }}</td>
       </tr>

       <!-- Goals -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Goals</td>
           <td style="padding: 6px; border: 1px solid black">{{ $goals }}</td>
       </tr>

       <!-- Objectives -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Objectives</td>
           <td style="padding: 6px; border: 1px solid black">{{ $objectives }}</td>
       </tr>

       <!-- Main Activities -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Main Activities</td>
           <td style="padding: 6px; border: 1px solid black">{!! nl2br(e($activities)) !!}</td>
       </tr>

       <!-- Implementing Organization -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Implementing
               Organization</td>
           <td style="padding: 6px; border: 1px solid black">{{ $implementing_org }}</td>
       </tr>

       <!-- Funded by -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Funded by</td>
           <td style="padding: 6px; border: 1px solid black">{{ $funder }}</td>
       </tr>

       <!-- Total Budget -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Total Budget</td>
           <td style="padding: 6px; border: 1px solid black">{{ $budget }}</td>
       </tr>

       <!-- Duration (Nested with border) -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; vertical-align: top; border: 1px solid black">
               Duration</td>
           <td style="padding: 0px; border: 1px solid black">
               <table width="100%" style="border-collapse: collapse; font-size: 13px; border: 1px solid black">
                   <tr>
                       <td style="padding: 6px; border: 1px solid black width: 30%;"><strong>Start
                               Date:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $start_date }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 6px; border: 1px solid black;"><strong>Finish Date:</strong></td>
                       <td style="padding: 6px; border: 1px solid black;">{{ $end_date }}</td>
                   </tr>
               </table>
           </td>
       </tr>

       <!-- MOU Date -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Date of signing of MOU
           </td>
           <td style="padding: 6px; border: 1px solid black">{{ $mou_date }}</td>
       </tr>

       <!-- Location -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Location</td>
           <td style="padding: 6px; border: 1px solid black">{{ $location }}</td>
       </tr>

       <!-- Provinces covered -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Provinces covered</td>
           <td style="padding: 6px; border: 1px solid black">{{ $provinces }}</td>
       </tr>

       <!-- Areas covered -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Areas, Villages or
               Districts covered</td>
           <td style="padding: 6px; border: 1px solid black">{{ $areas }}</td>
       </tr>

       <!-- Beneficiaries -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">No. of Direct
               Beneficiaries</td>
           <td style="padding: 6px; border: 1px solid black">{{ $direct_beneficiaries }}</td>
       </tr>
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">No. of Indirect
               Beneficiaries</td>
           <td style="padding: 6px; border: 1px solid black">{{ $indirect_beneficiaries }}</td>
       </tr>

       <!-- Project Staff (Nested with border) -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; vertical-align: top; border: 1px solid black">
               Project Staff (with provincial staff)</td>
           <td style="padding: 1px; border: 1px solid black">
               <table width="100%" style="border-collapse: collapse; font-size: 13px; border: 1px solid black">
                   <tr>
                       <td style="padding: 6px; border: 1px solid black width: 40%;"><strong>Organizational
                               structure:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $org_structure }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 6px; border: 1px solid black"><strong>No. and type of Health
                               Staff:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $health_staff }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 6px; border: 1px solid black"><strong>No. & type of Administrative
                               Staff:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $admin_staff }}</td>
                   </tr>
               </table>
           </td>
       </tr>

       <!-- Action Plan -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Action Plan</td>
           <td style="padding: 6px; border: 1px solid black">{!! nl2br(e($action_plan)) !!}</td>
       </tr>
   </table>
